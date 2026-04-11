<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\Categories;

use bantu\IniGetWrapper\IniGetWrapper;
use OCP\IL10N;

/**
 * Class php
 *
 * @package OCA\Survey_Client\Categories
 */
class Php implements ICategory {
	public function __construct(
		protected IniGetWrapper $phpIni,
		protected IL10N $l,
	) {
	}

	#[\Override]
	public function getCategory(): string {
		return 'php';
	}

	#[\Override]
	public function getDisplayName(): string {
		return $this->l->t('PHP environment <em>(version, memory limit, max. execution time, max. file size)</em>');
	}

	/**
	 * @return array<string, string|int>
	 */
	#[\Override]
	public function getData(): array {
		return [
			'version' => $this->cleanVersion(PHP_VERSION),
			'memory_limit' => $this->phpIni->getBytes('memory_limit'),
			'max_execution_time' => $this->phpIni->getNumeric('max_execution_time'),
			'upload_max_filesize' => $this->phpIni->getBytes('upload_max_filesize'),
		];
	}

	/**
	 * Try to strip away additional information
	 *
	 * @param string $version E.g. `5.5.30-1+deb.sury.org~trusty+1`
	 * @return string `5.5.30`
	 */
	protected function cleanVersion(string $version): string {
		$matches = [];
		preg_match('/^(\d+)(\.\d+)(\.\d+)/', $version, $matches);
		if (isset($matches[0])) {
			return $matches[0];
		}

		return $version;
	}
}
