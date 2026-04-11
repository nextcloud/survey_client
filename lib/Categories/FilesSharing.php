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
 * Class FilesSharing
 *
 * @package OCA\Survey_Client\Categories
 */
class FilesSharing implements ICategory {
	public function __construct(
		protected IDBConnection $connection,
		protected IL10N $l,
	) {
	}

	#[\Override]
	public function getCategory(): string {
		return 'files_sharing';
	}

	#[\Override]
	public function getDisplayName(): string {
		return $this->l->t('Number of shares <em>(per type and permission setting)</em>');
	}

	/**
	 * @return array<string, string|int>
	 */
	#[\Override]
	public function getData(): array {
		$query = $this->connection->getQueryBuilder();
		$query->select($query->func()->count('*', 'num_entries'))
			->addSelect(['permissions', 'share_type'])
			->from('share')
			->addGroupBy('permissions')
			->addGroupBy('share_type');
		$result = $query->executeQuery();

		$data = [
			'num_shares' => $this->countEntries('share'),
			'num_shares_user' => $this->countShares(0),
			'num_shares_groups' => $this->countShares(1),
			'num_shares_link' => $this->countShares(3),
			'num_shares_link_no_password' => $this->countShares(3, true),
			'num_fed_shares_sent' => $this->countShares(6),
			'num_fed_shares_received' => $this->countEntries('share_external'),
		];
		while ($row = $result->fetch()) {
			$data['permissions_' . $row['share_type'] . '_' . $row['permissions']] = $row['num_entries'];
		}
		$result->closeCursor();

		return $data;
	}

	protected function countEntries(string $tableName): int {
		$query = $this->connection->getQueryBuilder();
		$query->select($query->func()->count('*', 'num_entries'))
			->from($tableName);
		$result = $query->executeQuery();
		$row = $result->fetch();
		$result->closeCursor();

		return (int)$row['num_entries'];
	}

	protected function countShares(int $type, bool $noShareWith = false): int {
		$query = $this->connection->getQueryBuilder();
		$query->select($query->func()->count('*', 'num_entries'))
			->from('share')
			->where($query->expr()->eq('share_type', $query->createNamedParameter($type)));

		if ($noShareWith) {
			$query->andWhere($query->expr()->isNull('share_with'));
		}

		$result = $query->executeQuery();
		$row = $result->fetch();
		$result->closeCursor();

		return (int)$row['num_entries'];
	}
}
