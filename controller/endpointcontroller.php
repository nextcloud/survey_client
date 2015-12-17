<?php
/**
 * @author Joas Schilling <nickvergessen@owncloud.com>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
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

namespace OCA\PopularityContestClient\Controller;

use OCA\PopularityContestClient\Collector;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\BackgroundJob\IJobList;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use OCP\IRequest;

class EndpointController extends Controller {

	const SURVEY_SERVER_URL = 'http://localhost/ownCloud/master/core/';

	/** @var Collector */
	protected $collector;

	/** @var IClientService */
	protected $clientService;

	/** @var IConfig */
	protected $config;

	/** @var IJobList */
	protected $jobList;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param Collector $collector
	 * @param IClientService $clientService
	 * @param IConfig $config
	 * @param IJobList $jobList
	 */
	public function __construct($appName, IRequest $request, Collector $collector, IClientService $clientService, IConfig $config, IJobList $jobList) {
		parent::__construct($appName, $request);

		$this->collector = $collector;
		$this->clientService = $clientService;
		$this->config = $config;
		$this->jobList = $jobList;
	}

	/**
	 * @return \OC_OCS_Result
	 */
	public function enableMonthly() {
		$this->jobList->add('OCA\PopularityContestClient\MonthlyReport');
		return new \OC_OCS_Result();
	}

	/**
	 * @return \OC_OCS_Result
	 */
	public function disableMonthly() {
		$this->jobList->remove('OCA\PopularityContestClient\MonthlyReport');
		return new \OC_OCS_Result();
	}

	/**
	 * @return \OC_OCS_Result
	 */
	public function sendReport() {
		$report = $this->collector->getReport();

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
