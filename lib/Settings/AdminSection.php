<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Survey_Client\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection {
	public function __construct(
		protected IL10N $l,
		protected IURLGenerator $url,
	) {
	}

	/**
	 * returns the ID of the section. It is supposed to be a lower case string
	 *
	 * @returns string
	 */
	#[\Override]
	public function getID(): string {
		return 'survey_client';
	}

	/**
	 * returns the translated name as it should be displayed, e.g. 'LDAP / AD
	 * integration'. Use the L10N service to translate it.
	 *
	 * @return string
	 */
	#[\Override]
	public function getName(): string {
		return $this->l->t('Usage survey');
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 *             the settings navigation. The sections are arranged in ascending order of
	 *             the priority values. It is required to return a value between 0 and 99.
	 */
	#[\Override]
	public function getPriority(): int {
		return 80;
	}

	/**
	 * {@inheritdoc}
	 */
	#[\Override]
	public function getIcon(): string {
		return $this->url->imagePath('survey_client', 'app-dark.svg');
	}
}
