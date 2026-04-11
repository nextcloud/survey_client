<?php

declare(strict_types=1);

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
	public function __construct(
		protected IConfig $config,
		protected IL10N $l,
	) {
	}

	#[\Override]
	public function getCategory(): string {
		return 'encryption';
	}

	#[\Override]
	public function getDisplayName(): string {
		return $this->l->t('Encryption information <em>(is it enabled?, what is the default module)</em>');
	}

	/**
	 * @return array<string, string|int>
	 */
	#[\Override]
	public function getData(): array {
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
