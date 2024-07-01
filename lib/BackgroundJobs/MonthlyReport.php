<?php
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\BackgroundJobs;

use OCA\Survey_Client\Collector;
use OCP\AppFramework\Http;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\IJob;
use OCP\BackgroundJob\TimedJob;
use Psr\Log\LoggerInterface;

class MonthlyReport extends TimedJob {
	protected Collector $collector;
	protected LoggerInterface $logger;

	public function __construct(ITimeFactory $time,
		Collector $collector,
		LoggerInterface $logger) {
		parent::__construct($time);
		$this->collector = $collector;
		$this->logger = $logger;
		// Run all 28 days
		$this->setInterval(28 * 24 * 60 * 60);
		// keeping time sensitive to not overload the target server at a single specific time of the day
		$this->setTimeSensitivity(IJob::TIME_SENSITIVE);
	}

	protected function run($argument) {
		$result = $this->collector->sendReport();

		if ($result->getStatus() !== Http::STATUS_OK) {
			$this->logger->info('Error while sending usage statistic');
		}
	}
}
