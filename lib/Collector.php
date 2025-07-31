<?php
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client;

use bantu\IniGetWrapper\IniGetWrapper;
use OCA\Survey_Client\Categories\Apps;
use OCA\Survey_Client\Categories\Database;
use OCA\Survey_Client\Categories\Encryption;
use OCA\Survey_Client\Categories\FilesSharing;
use OCA\Survey_Client\Categories\ICategory;
use OCA\Survey_Client\Categories\Php;
use OCA\Survey_Client\Categories\Server;
use OCA\Survey_Client\Categories\Stats;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Services\IAppConfig;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IL10N;

class Collector {
	public const SURVEY_SERVER_URL = 'https://surveyserver.nextcloud.com/';

	/** @var ICategory[] */
	protected array $categories;

	public function __construct(
		protected IClientService $clientService,
		protected IConfig $config,
		protected IAppConfig $appConfig,
		protected IDBConnection $connection,
		protected IniGetWrapper $phpIni,
		protected IL10N $l,
		protected ITimeFactory $timeFactory,
	) {
	}

	protected function registerCategories(): void {
		$this->categories[] = new Server(
			$this->config,
			$this->l
		);
		$this->categories[] = new Php(
			$this->phpIni,
			$this->l
		);
		$this->categories[] = new Database(
			$this->config,
			$this->connection,
			$this->l
		);
		$this->categories[] = new Apps(
			$this->connection,
			$this->l
		);
		$this->categories[] = new Stats(
			$this->connection,
			$this->l
		);
		$this->categories[] = new FilesSharing(
			$this->connection,
			$this->l
		);
		$this->categories[] = new Encryption(
			$this->config,
			$this->l
		);
	}

	/**
	 * @return array<string, array{displayName: string, enabled: bool}>
	 */
	public function getCategories(): array {
		$this->registerCategories();

		$categories = [];

		foreach ($this->categories as $category) {
			$categories[$category->getCategory()] = [
				'displayName' => $category->getDisplayName(),
				'enabled' => $this->appConfig->getAppValueBool($category->getCategory(), true),
			];
		}

		return $categories;
	}

	/**
	 * @return array{id: string, items: array}
	 */
	public function getReport() {
		$this->registerCategories();

		$tuples = [];
		foreach ($this->categories as $category) {
			if ($this->appConfig->getAppValueBool($category->getCategory(), true)) {
				foreach ($category->getData() as $key => $value) {
					$tuples[] = [
						$category->getCategory(),
						$key,
						$value
					];
				}
			}
		}

		return [
			'id' => $this->config->getSystemValue('instanceid'),
			'items' => $tuples,
		];
	}

	/**
	 * @return DataResponse
	 */
	public function sendReport(): DataResponse {
		$report = $this->getReport();

		$client = $this->clientService->newClient();

		try {
			$response = $client->post(self::SURVEY_SERVER_URL . 'ocs/v2.php/apps/survey_server/api/v1/survey', [
				'timeout' => 5,
				'query' => [
					'data' => json_encode($report),
				],
			]);
		} catch (\Exception $e) {
			return new DataResponse(
				$report,
				Http::STATUS_INTERNAL_SERVER_ERROR
			);
		}

		if ($response->getStatusCode() === Http::STATUS_OK) {
			$this->appConfig->setAppValueInt('last_sent', $this->timeFactory->getTime());
			$this->appConfig->setAppValueString('last_report', json_encode($report), true);
			return new DataResponse(
				$report
			);
		}

		return new DataResponse(
			$report,
			Http::STATUS_INTERNAL_SERVER_ERROR
		);
	}
}
