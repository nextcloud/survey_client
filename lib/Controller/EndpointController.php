<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\Controller;

use OCA\Survey_Client\BackgroundJobs\MonthlyReport;
use OCA\Survey_Client\Collector;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\BackgroundJob\IJobList;
use OCP\IRequest;
use OCP\Notification\IManager;

class EndpointController extends OCSController {

	/** @var Collector */
	protected $collector;

	/** @var IJobList */
	protected $jobList;

	/** @var IManager */
	protected $manager;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param Collector $collector
	 * @param IJobList $jobList
	 * @param IManager $manager
	 */
	public function __construct(string $appName,
		IRequest $request,
		Collector $collector,
		IJobList $jobList,
		IManager $manager) {
		parent::__construct($appName, $request);

		$this->collector = $collector;
		$this->jobList = $jobList;
		$this->manager = $manager;
	}

	/**
	 * @return DataResponse
	 */
	public function enableMonthly(): DataResponse {
		$this->jobList->add(MonthlyReport::class);

		$notification = $this->manager->createNotification();
		$notification->setApp('survey_client');
		$this->manager->markProcessed($notification);

		return new DataResponse();
	}

	/**
	 * @return DataResponse
	 */
	public function disableMonthly(): DataResponse {
		$this->jobList->remove(MonthlyReport::class);

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
