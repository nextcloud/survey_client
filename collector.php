<?php
/**
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\PopularityContestClient;

use bantu\IniGetWrapper\IniGetWrapper;
use OCA\PopularityContestClient\Categories\Apps;
use OCA\PopularityContestClient\Categories\Database;
use OCA\PopularityContestClient\Categories\Encryption;
use OCA\PopularityContestClient\Categories\Files_Sharing;
use OCA\PopularityContestClient\Categories\ICategory;
use OCA\PopularityContestClient\Categories\OwnCloud;
use OCA\PopularityContestClient\Categories\php;
use OCA\PopularityContestClient\Categories\Stats;
use OCP\AppFramework\Http;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IL10N;

class Collector {

	const SURVEY_SERVER_URL = 'http://localhost/ownCloud/master/core/';

	/** @var ICategory[] */
	protected $categories;

	/** @var IClientService */
	protected $clientService;

	/** @var IConfig */
	protected $config;
	
	/** @var IDBConnection */
	protected $connection;

	/** @var IniGetWrapper */
	protected $phpIni;

	/** @var \OCP\IL10N */
	protected $l;

	/**
	 * Collector constructor.
	 *
	 * @param IClientService $clientService
	 * @param IConfig $config
	 * @param IDBConnection $connection
	 * @param IniGetWrapper $phpIni
	 * @param IL10N $l
	 */
	public function __construct(IClientService $clientService, IConfig $config, IDBConnection $connection, IniGetWrapper $phpIni, IL10N $l) {
		$this->clientService = $clientService;
		$this->config = $config;
		$this->connection = $connection;
		$this->phpIni = $phpIni;
		$this->l = $l;
	}

	protected function registerCategories() {
		$this->categories[] = new OwnCloud(
			$this->config,
			$this->l
		);
		$this->categories[] = new php(
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
		$this->categories[] = new Files_Sharing(
			$this->connection,
			$this->l
		);
		$this->categories[] = new Encryption(
			$this->config,
			$this->l
		);
	}

	/**
	 * @return array
	 */
	public function getCategories() {
		$this->registerCategories();

		$categories = [];

		foreach ($this->categories as $category) {
			$categories[$category->getCategory()] = [
				'displayName'	=> $category->getDisplayName(),
				'enabled'		=> $this->config->getAppValue('popularitycontestclient', $category->getCategory(), 'yes') === 'yes',
			];
		}

		return $categories;
	}

	/**
	 * @return array
	 */
	public function getReport() {
		$this->registerCategories();

		$tuples = [];
		foreach ($this->categories as $category) {
			if ($this->config->getAppValue('popularitycontestclient', $category->getCategory(), 'yes') === 'yes') {
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
	 * @return \OC_OCS_Result
	 */
	public function sendReport() {
		$report = $this->getReport();

		$client = $this->clientService->newClient();
		$this->config->setAppValue('popularitycontestclient', 'last_sent', time());
		$this->config->setAppValue('popularitycontestclient', 'last_report', json_encode($report));

		try {
			$response = $client->post(self::SURVEY_SERVER_URL . 'ocs/v2.php/apps/popularitycontestserver/api/v1/survey', [
				'timeout' => 5,
				'query' => [
					'data' => json_encode($report),
				],
			]);
		} catch (\Exception $e) {
			return new \OC_OCS_Result(
				$report,
				Http::STATUS_INTERNAL_SERVER_ERROR
			);
		}

		if ($response->getStatusCode() === Http::STATUS_OK) {
			return new \OC_OCS_Result(
				$report,
				100// HTTP::STATUS_OK, TODO: <status>failure</status><statuscode>200</statuscode>
			);
		}

		return new \OC_OCS_Result(
			$report,
			Http::STATUS_INTERNAL_SERVER_ERROR
		);
	}
}
