<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\Controller;

use OCA\Survey_Client\BackgroundJobs\AdminNotification;
use OCA\Survey_Client\BackgroundJobs\MonthlyReport;
use OCA\Survey_Client\Collector;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\AppFramework\Services\IAppConfig;
use OCP\BackgroundJob\IJobList;
use OCP\IRequest;
use OCP\Notification\IManager;

class EndpointController extends OCSController {
	public function __construct(
		string $appName,
		IRequest $request,
		protected Collector $collector,
		protected IJobList $jobList,
		protected IManager $manager,
		protected IAppConfig $appConfig,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @return DataResponse
	 */
	public function enableMonthly(): DataResponse {
		$this->jobList->add(MonthlyReport::class);
		$this->appConfig->deleteAppValue('never_again');

		$notification = $this->manager->createNotification();
		$notification->setApp('survey_client');
		$this->manager->markProcessed($notification);

		return new DataResponse();
	}

	/**
	 * @return DataResponse
	 */
	public function disableMonthly(bool $never = false): DataResponse {
		$this->jobList->remove(MonthlyReport::class);
		if ($never) {
			$this->jobList->remove(AdminNotification::class);
			$this->appConfig->setAppValueBool('never_again', true);
		}

		$notification = $this->manager->createNotification();
		$notification->setApp('survey_client');
		$this->manager->markProcessed($notification);

		return new DataResponse();
	}

	/**
	 * @return DataResponse
	 */
	public function sendReport(): DataResponse {
		return $this->collector->sendReport();
	}
}
