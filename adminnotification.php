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

namespace OCA\PopularityContestClient;

use OC\BackgroundJob\QueuedJob;

class AdminNotification extends QueuedJob {
	protected function run($argument) {
		$manager = \OC::$server->getNotificationManager();
		$urlGenerator = \OC::$server->getURLGenerator();

		$notification = $manager->createNotification();
		$notification->setApp('popularitycontestclient')
			->setDateTime(new \DateTime())
			->setSubject('updated')
			->setObject('dummy', 23)
			->setLink($urlGenerator->getAbsoluteURL('index.php/settings/admin#goto-usage-report'));

		$enableAction = $notification->createAction();
		$enableAction->setLabel('enable')
			->setLink($urlGenerator->getAbsoluteURL('ocs/v2.php/apps/popularitycontestclient/api/v1/monthly'), 'POST')
			->setPrimary(true);
		$notification->addAction($enableAction);

		$disableAction = $notification->createAction();
		$disableAction->setLabel('disable')
			->setLink($urlGenerator->getAbsoluteURL('ocs/v2.php/apps/popularitycontestclient/api/v1/monthly'), 'DELETE')
			->setPrimary(false);
		$notification->addAction($disableAction);

		$adminGroup = \OC::$server->getGroupManager()->get('admin');
		foreach ($adminGroup->getUsers() as $admin) {
			$notification->setUser($admin->getUID());
			$manager->notify($notification);
		}

	}
}
