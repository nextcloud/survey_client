<?php
/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */


namespace OCA\Survey_Client\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection {

	/** @var IL10N */
	private $l;
	/** @var IURLGenerator */
	private $url;

	public function __construct(IL10N $l, IURLGenerator $url) {
		$this->l = $l;
		$this->url = $url;
	}

	/**
	 * returns the ID of the section. It is supposed to be a lower case string
	 *
	 * @returns string
	 */
	public function getID() {
		return 'survey_client';
	}

	/**
	 * returns the translated name as it should be displayed, e.g. 'LDAP / AD
	 * integration'. Use the L10N service to translate it.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->l->t('Usage survey');
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 *             the settings navigation. The sections are arranged in ascending order of
	 *             the priority values. It is required to return a value between 0 and 99.
	 */
	public function getPriority() {
		return 80;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIcon() {
		return $this->url->imagePath('survey_client', 'app-dark.svg');
	}
}
