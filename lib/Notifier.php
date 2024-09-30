<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client;

use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;
use OCP\Notification\UnknownNotificationException;

class Notifier implements INotifier {
	public function __construct(
		protected IFactory $l10nFactory,
		protected IURLGenerator $url,
	) {
	}

	/**
	 * Identifier of the notifier, only use [a-z0-9_]
	 *
	 * @return string
	 * @since 17.0.0
	 */
	public function getID(): string {
		return 'survey_client';
	}

	/**
	 * Human-readable name describing the notifier
	 *
	 * @return string
	 * @since 17.0.0
	 */
	public function getName(): string {
		return $this->l10nFactory->get('survey_client')->t('Usage survey');
	}

	/**
	 * @param INotification $notification
	 * @param string $languageCode The code of the language that should be used to prepare the notification
	 * @return INotification
	 * @throws UnknownNotificationException When the notification was not prepared by a notifier
	 */
	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== 'survey_client') {
			// Not my app => throw
			throw new UnknownNotificationException();
		}

		// Read the language from the notification
		$l = $this->l10nFactory->get('survey_client', $languageCode);

		$notification->setParsedSubject($l->t('Help improve Nextcloud'))
			->setParsedMessage($l->t('Do you want to help us to improve Nextcloud by providing some anonymized data about your setup and usage? You can disable it at any time in the admin settings again.'))
			->setLink($this->url->linkToRouteAbsolute('settings.AdminSettings.index', ['section' => 'survey_client']))
			->setIcon($this->url->getAbsoluteURL($this->url->imagePath('survey_client', 'app-dark.svg')));

		$enableAction = $notification->createAction();
		$enableAction->setLabel('enable')
			->setParsedLabel($l->t('Send usage'))
			->setLink($this->url->linkToOCSRouteAbsolute('survey_client.Endpoint.enableMonthly'), 'POST')
			->setPrimary(true);
		$notification->addParsedAction($enableAction);

		$disableAction = $notification->createAction();
		$disableAction->setLabel('disable')
			->setParsedLabel($l->t('Not now'))
			->setLink($this->url->linkToOCSRouteAbsolute('survey_client.Endpoint.disableMonthly'), 'DELETE')
			->setPrimary(false);
		$notification->addParsedAction($disableAction);

		return $notification;
	}
}
