<?php
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
	/** @var IDBConnection */
	protected $connection;

	/** @var \OCP\IL10N */
	protected $l;

	/**
	 * @param IDBConnection $connection
	 * @param IL10N $l
	 */
	public function __construct(IDBConnection $connection, IL10N $l) {
		$this->connection = $connection;
		$this->l = $l;
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return 'files_sharing';
	}

	/**
	 * @return string
	 */
	public function getDisplayName() {
		return $this->l->t('Number of shares <em>(per type and permission setting)</em>');
	}

	/**
	 * @return array (string => string|int)
	 */
	public function getData() {
		$query = $this->connection->getQueryBuilder();
		$query->selectAlias($query->createFunction('COUNT(*)'), 'num_entries')
			->addSelect(['permissions', 'share_type'])
			->from('share')
			->addGroupBy('permissions')
			->addGroupBy('share_type');
		$result = $query->execute();

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

	/**
	 * @param string $tableName
	 * @return int
	 */
	protected function countEntries($tableName) {
		$query = $this->connection->getQueryBuilder();
		$query->selectAlias($query->createFunction('COUNT(*)'), 'num_entries')
			->from($tableName);
		$result = $query->execute();
		$row = $result->fetch();
		$result->closeCursor();

		return (int)$row['num_entries'];
	}

	/**
	 * @param int $type
	 * @param bool $noShareWith
	 * @return int
	 */
	protected function countShares($type, $noShareWith = false) {
		$query = $this->connection->getQueryBuilder();
		$query->selectAlias($query->createFunction('COUNT(*)'), 'num_entries')
			->from('share')
			->where($query->expr()->eq('share_type', $query->createNamedParameter($type)));

		if ($noShareWith) {
			$query->andWhere($query->expr()->isNull('share_with'));
		}

		$result = $query->execute();
		$row = $result->fetch();
		$result->closeCursor();

		return (int)$row['num_entries'];
	}
}
