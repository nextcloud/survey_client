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

namespace OCA\Survey_Client\BackgroundJobs;

use OC\BackgroundJob\TimedJob;
use OCA\Survey_Client\AppInfo\Application;
use OCP\AppFramework\Http;
use OCP\BackgroundJob\IJob;

class MonthlyReport extends TimedJob {

	/**
	 * MonthlyReport constructor.
	 */
	public function __construct() {
		// Run all 28 days
		$this->setInterval(28 * 24 * 60 * 60);
		// keeping time sensitive to not overload the target server at a single specific time of the day
		$this->setTimeSensitivity(IJob::TIME_SENSITIVE);
	}

	protected function run($argument) {
		$application = new Application();
		/** @var \OCA\Survey_Client\Collector $collector */
		$collector = $application->getContainer()->query('OCA\Survey_Client\Collector');
		$result = $collector->sendReport();

		if ($result->getStatus() !== Http::STATUS_OK) {
			\OC::$server->getLogger()->info('Error while sending usage statistic');
		}
	}
}
