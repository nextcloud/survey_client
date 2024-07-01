<?php
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\Categories;

use OCP\IConfig;
use OCP\IL10N;

/**
 * Class Encryption
 *
 * @package OCA\Survey_Client\Categories
 */
class Encryption implements ICategory {
	/** @var \OCP\IConfig */
	protected $config;

	/** @var \OCP\IL10N */
	protected $l;

	/**
	 * @param IConfig $config
	 * @param IL10N $l
	 */
	public function __construct(IConfig $config, IL10N $l) {
		$this->config = $config;
		$this->l = $l;
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return 'encryption';
	}

	/**
	 * @return string
	 */
	public function getDisplayName() {
		return $this->l->t('Encryption information <em>(is it enabled?, what is the default module)</em>');
	}

	/**
	 * @return array (string => string|int)
	 */
	public function getData() {
		$data = [
			'enabled' => $this->config->getAppValue('core', 'encryption_enabled', 'no') === 'yes' ? 'yes' : 'no',
			'default_module' => $this->config->getAppValue('core', 'default_encryption_module') === 'OC_DEFAULT_MODULE'  ? 'yes' : 'no',
		];

		if ($data['enabled'] === 'yes') {
			unset($data['default_module']);
		}

		return $data;
	}
}
