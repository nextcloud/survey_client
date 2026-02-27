<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Survey_Client\Migration;

use OCA\Survey_Client\BackgroundJobs\AdminNotification;
use OCA\Survey_Client\BackgroundJobs\MonthlyReport;
use OCP\AppFramework\Services\IAppConfig;
use OCP\BackgroundJob\IJobList;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class SendAdminNotification implements IRepairStep {
	public function __construct(
		protected IJobList $jobList,
		protected IAppConfig $appConfig,
	) {
	}

	public function getName(): string {
		return 'Send an admin notification if monthly report is disabled';
	}

	public function run(IOutput $output): void {
		if ($this->appConfig->getAppValueBool('never_again')) {
			return;
		}

		if (!$this->jobList->has(MonthlyReport::class, null)) {
			$this->jobList->add(AdminNotification::class);
		}
	}
}
