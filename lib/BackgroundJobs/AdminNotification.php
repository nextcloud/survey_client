<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\BackgroundJobs;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\QueuedJob;
use OCP\IGroupManager;
use OCP\Notification\IManager;

class AdminNotification extends QueuedJob {
	public function __construct(
		ITimeFactory $time,
		protected IManager $manager,
		protected IGroupManager $groupManager,
	) {
		parent::__construct($time);
	}

	protected function run($argument): void {
		$notification = $this->manager->createNotification();

		$notification->setApp('survey_client')
			->setDateTime($this->time->getDateTime())
			->setSubject('updated')
			->setObject('dummy', '23');

		$adminGroup = $this->groupManager->get('admin');
		foreach ($adminGroup->getUsers() as $admin) {
			$notification->setUser($admin->getUID());
			$this->manager->notify($notification);
		}
	}
}
