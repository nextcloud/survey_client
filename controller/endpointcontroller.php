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
use OCP\Http\Client\IClientService;
use OCP\IRequest;

class EndpointController extends Controller {

	const SURVEY_SERVER_URL = 'http://localhost/ownCloud/master/core/';

	/** @var Collector */
	protected $collector;

	/** @var IClientService */
	protected $clientService;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param Collector $collector
	 * @param IClientService $clientService
	 */
	public function __construct($appName, IRequest $request, Collector $collector, IClientService $clientService) {
		parent::__construct($appName, $request);

		$this->collector = $collector;
		$this->clientService = $clientService;
	}

	/**
	 * @return \OC_OCS_Result
	 */
	public function sendReport() {
		$report = $this->collector->getReport();

		$client = $this->clientService->newClient();
		$response = $client->post(self::SURVEY_SERVER_URL . 'ocs/v2.php/apps/popularitycontestserver/api/v1/survey', [
			'timeout' => 5,
			'query' => [
				'data' => json_encode($report),
			],
		]);

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
