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

namespace OCA\Survey_Client;

use OCP\L10N\IFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;

class Notifier implements INotifier {

	/** @var IFactory */
	protected $l10nFactory;

	/**
	 * Notifier constructor.
	 *
	 * @param IFactory $l10nFactory
	 */
	public function __construct(IFactory $l10nFactory) {
		$this->l10nFactory = $l10nFactory;
	}

	/**
	 * @param INotification $notification
	 * @param string $languageCode The code of the language that should be used to prepare the notification
	 * @return INotification
	 * @throws \InvalidArgumentException When the notification was not prepared by a notifier
	 */
	public function prepare(INotification $notification, $languageCode) {
		if ($notification->getApp() !== 'survey_client') {
			// Not my app => throw
			throw new \InvalidArgumentException();
		}

		// Read the language from the notification
		$l = $this->l10nFactory->get('survey_client', $languageCode);

		$notification->setParsedSubject((string) $l->t(
			'Do you want to help us to improve Nextcloud by providing some anonymized data about your setup and usage? You can disable it at any time in the admin settings again.'));

		foreach ($notification->getActions() as $action) {
			if ($action->getLabel() === 'disable') {
				$action->setParsedLabel((string) $l->t('Not now'));
			} else if ($action->getLabel() === 'enable') {
				$action->setParsedLabel((string) $l->t('Send usage'));
			}
			$notification->addParsedAction($action);
		}

		return $notification;
	}
}
