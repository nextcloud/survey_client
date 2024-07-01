<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Survey_Client\Migration;

use OCA\Survey_Client\BackgroundJobs\AdminNotification;
use OCA\Survey_Client\BackgroundJobs\MonthlyReport;
use OCP\BackgroundJob\IJobList;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class SendAdminNotification implements IRepairStep {
	/** @var IJobList */
	private $jobList;

	public function __construct(IJobList $jobList) {
		$this->jobList = $jobList;
	}

	public function getName(): string {
		return 'Send an admin notification if monthly report is disabled';
	}

	public function run(IOutput $output): void {
		if (!$this->jobList->has(MonthlyReport::class, null)) {
			$this->jobList->add(AdminNotification::class);
		}
	}
}
