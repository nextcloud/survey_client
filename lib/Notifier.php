<?php
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

class Notifier implements INotifier {

	/** @var IFactory */
	protected $l10nFactory;

	/** @var IURLGenerator */
	protected $url;

	/**
	 * Notifier constructor.
	 *
	 * @param IFactory $l10nFactory
	 * @param IURLGenerator $url
	 */
	public function __construct(IFactory $l10nFactory, IURLGenerator $url) {
		$this->l10nFactory = $l10nFactory;
		$this->url = $url;
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
	 * Human readable name describing the notifier
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
	 * @throws \InvalidArgumentException When the notification was not prepared by a notifier
	 */
	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== 'survey_client') {
			// Not my app => throw
			throw new \InvalidArgumentException();
		}

		// Read the language from the notification
		$l = $this->l10nFactory->get('survey_client', $languageCode);

		$notification->setParsedSubject($l->t('Help improve Nextcloud'))
			->setParsedMessage($l->t('Do you want to help us to improve Nextcloud by providing some anonymized data about your setup and usage? You can disable it at any time in the admin settings again.'))
			->setLink($this->url->linkToRoute('settings.AdminSettings.index', ['section' => 'survey_client']))
			->setIcon($this->url->imagePath('survey_client', 'app-dark.svg'));

		foreach ($notification->getActions() as $action) {
			if ($action->getLabel() === 'disable') {
				$action->setParsedLabel($l->t('Not now'))
					->setLink($this->url->getAbsoluteURL('ocs/v2.php/apps/survey_client/api/v1/monthly'), 'DELETE');
			} elseif ($action->getLabel() === 'enable') {
				$action->setParsedLabel($l->t('Send usage'))
					->setLink($this->url->getAbsoluteURL('ocs/v2.php/apps/survey_client/api/v1/monthly'), 'POST');
			}
			$notification->addParsedAction($action);
		}

		return $notification;
	}
}
