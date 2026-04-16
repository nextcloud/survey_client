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
 * Class Server
 *
 * @package OCA\Survey_Client\Categories
 */
class Server implements ICategory {
	public function __construct(
		protected IConfig $config,
		protected IL10N $l,
	) {
	}

	#[\Override]
	public function getCategory(): string {
		return 'server';
	}

	#[\Override]
	public function getDisplayName(): string {
		return $this->l->t('Server instance details <em>(version, memcache used, status of locking/previews/avatars)</em>');
	}

	/**
	 * @return array<string, string|int>
	 */
	#[\Override]
	public function getData(): array {
		return [
			'version' => $this->config->getSystemValue('version'),
			'code' => $this->codeLocation(),
			'enable_avatars' => $this->config->getSystemValue('enable_avatars', true) ? 'yes' : 'no',
			'enable_previews' => $this->config->getSystemValue('enable_previews', true) ? 'yes' : 'no',
			'memcache.local' => $this->config->getSystemValue('memcache.local', 'none'),
			'memcache.distributed' => $this->config->getSystemValue('memcache.distributed', 'none'),
			'asset-pipeline.enabled' => $this->config->getSystemValue('asset-pipeline.enabled') ? 'yes' : 'no',
			'filelocking.enabled' => $this->config->getSystemValue('filelocking.enabled', true) ? 'yes' : 'no',
			'memcache.locking' => $this->config->getSystemValue('memcache.locking', 'none'),
			'debug' => $this->config->getSystemValue('debug', false) ? 'yes' : 'no',
			'cron' => $this->config->getAppValue('core', 'backgroundjobs_mode', 'ajax'),
		];
	}

	protected function codeLocation(): string {
		if (file_exists(\OC::$SERVERROOT . '/.git') && is_dir(\OC::$SERVERROOT . '/.git')) {
			return 'git';
		}
		return 'other';
	}
}
