<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\Categories;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IL10N;

/**
 * Class Apps
 *
 * @package OCA\Survey_Client\Categories
 */
class Apps implements ICategory {
	public function __construct(
		protected IDBConnection $connection,
		protected IL10N $l,
	) {
	}

	#[\Override]
	public function getCategory(): string {
		return 'apps';
	}

	#[\Override]
	public function getDisplayName(): string {
		return $this->l->t('App list <em>(for each app: name, version, enabled status)</em>');
	}

	/**
	 * @return array<string, string|int>
	 */
	#[\Override]
	public function getData(): array {
		$query = $this->connection->getQueryBuilder();

		$query->select('*')
			->from('appconfig')
			->where($query->expr()->in('configkey', $query->createNamedParameter(
				['enabled', 'installed_version'],
				IQueryBuilder::PARAM_STR_ARRAY
			)));
		$result = $query->execute();

		$data = [];
		while ($row = $result->fetch()) {
			if ($row['configkey'] === 'enabled' && $row['configvalue'] === 'no') {
				$data[$row['appid']] = 'disabled';
			}
			if ($row['configkey'] === 'installed_version' && !isset($data[$row['appid']])) {
				$data[$row['appid']] = $row['configvalue'];
			}
		}
		$result->closeCursor();

		return $data;
	}
}
