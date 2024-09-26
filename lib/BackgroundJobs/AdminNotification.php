<?php
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\BackgroundJobs;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\QueuedJob;
use OCP\IGroupManager;
use OCP\IURLGenerator;
use OCP\Notification\IManager;

class AdminNotification extends QueuedJob {
	protected IManager $manager;
	protected IGroupManager $groupManager;
	protected IURLGenerator $url;

	public function __construct(ITimeFactory $time,
		IManager $manager,
		IGroupManager $groupManager,
		IURLGenerator $url) {
		parent::__construct($time);
		$this->manager = $manager;
		$this->groupManager = $groupManager;
		$this->url = $url;
	}

	protected function run($argument): void {
		$notification = $this->manager->createNotification();

		$notification->setApp('survey_client')
			->setDateTime(new \DateTime())
			->setSubject('updated')
			->setObject('dummy', '23');

		$enableLink = $this->url->linkToOCSRouteAbsolute('survey_client.Endpoint.enableMonthly');
		$enableAction = $notification->createAction();
		$enableAction->setLabel('enable')
			->setLink($enableLink, 'POST')
			->setPrimary(true);
		$notification->addAction($enableAction);

		$disableLink = $this->url->linkToOCSRouteAbsolute('survey_client.Endpoint.disableMonthly');
		$disableAction = $notification->createAction();
		$disableAction->setLabel('disable')
			->setLink($disableLink, 'DELETE')
			->setPrimary(false);
		$notification->addAction($disableAction);

		$adminGroup = $this->groupManager->get('admin');
		foreach ($adminGroup->getUsers() as $admin) {
			$notification->setUser($admin->getUID());
			$this->manager->notify($notification);
		}
	}
}
