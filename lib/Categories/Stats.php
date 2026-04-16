<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\Categories;

use OCP\IDBConnection;
use OCP\IL10N;

/**
 * Class Stats
 *
 * @package OCA\Survey_Client\Categories
 */
class Stats implements ICategory {
	public function __construct(
		protected IDBConnection $connection,
		protected IL10N $l,
	) {
	}

	#[\Override]
	public function getCategory(): string {
		return 'stats';
	}

	#[\Override]
	public function getDisplayName(): string {
		return $this->l->t('Statistic <em>(number of files, users, storages per type, comments and tags)</em>');
	}

	/**
	 * @return array<string, string|int>
	 */
	#[\Override]
	public function getData(): array {
		return [
			'num_files' => $this->countEntries('filecache'),
			'num_users' => $this->countUserEntries(),
			'num_storages' => $this->countEntries('storages'),
			'num_storages_local' => $this->countStorages('local'),
			'num_storages_home' => $this->countStorages('home'),
			'num_storages_other' => $this->countStorages('other'),

			'num_comments' => $this->countEntries('comments'),
			'num_comment_markers' => $this->countEntries('comments_read_markers', 'user_id'),
			'num_systemtags' => $this->countEntries('systemtag'),
			'num_systemtags_mappings' => $this->countEntries('systemtag_object_mapping'),
		];
	}

	/**
	 * @return int
	 */
	protected function countUserEntries(): int {
		$query = $this->connection->getQueryBuilder();
		$query->select($query->func()->count('*', 'num_entries'))
			->from('preferences')
			->where($query->expr()->eq('configkey', $query->createNamedParameter('lastLogin')));
		$result = $query->executeQuery();
		$row = $result->fetch();
		$result->closeCursor();

		return (int)$row['num_entries'];
	}

	protected function countStorages(string $type): int {
		$query = $this->connection->getQueryBuilder();
		$query->select($query->func()->count('*', 'num_entries'))
			->from('storages');

		if ($type === 'home') {
			$query->where($query->expr()->like('id', $query->createNamedParameter('home::%')));
		} elseif ($type === 'local') {
			$query->where($query->expr()->like('id', $query->createNamedParameter('local::%')));
		} elseif ($type === 'other') {
			$query->where($query->expr()->notLike('id', $query->createNamedParameter('home::%')));
			$query->andWhere($query->expr()->notLike('id', $query->createNamedParameter('local::%')));
		}

		$result = $query->executeQuery();
		$row = $result->fetch();
		$result->closeCursor();

		return (int)$row['num_entries'];
	}

	/**
	 * @param string $tableName
	 * @param string $column
	 * @return int
	 */
	protected function countEntries(string $tableName, string $column = '*'): int {
		$query = $this->connection->getQueryBuilder();

		if ($column !== '*') {
			$column = 'DISTINCT(' . $query->getColumnName($column) . ')';
		}
		$query->selectAlias($query->createFunction('COUNT(' . $column . ')'), 'num_entries')
			->from($tableName);
		$result = $query->executeQuery();
		$row = $result->fetch();
		$result->closeCursor();

		return (int)$row['num_entries'];
	}
}
