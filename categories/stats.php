<?php
/**
 * @author Joas Schilling <nickvergessen@owncloud.com>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\PopularityContestClient\Categories;


use OCP\IDBConnection;
use OCP\IL10N;

/**
 * Class Stats
 *
 * @package OCA\PopularityContestClient\Categories
 */
class Stats implements ICategory {
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
		return 'stats';
	}

	/**
	 * @return string
	 */
	public function getDisplayName() {
		return (string) $this->l->t('Statistic (number of files, users, shares, etc.)');
	}

	/**
	 * @return array (string => string|int)
	 */
	public function getData() {
		return [
			'num_files' => $this->countEntries('filecache'),
			'num_users' => $this->countUserEntries(),
			'num_storages' => $this->countEntries('storages'),
			'num_storages_local' => $this->countStorages('local'),
			'num_storages_home' => $this->countStorages('home'),
			'num_storages_other' => $this->countStorages('other'),
			'num_shares' => $this->countEntries('share'),
			'num_shares_user' => $this->countShares(0),
			'num_shares_groups' => $this->countShares(1),
			'num_shares_link' => $this->countShares(3),
			'num_shares_link_no_password' => $this->countShares(3, true),
			'num_fed_shares_sent' => $this->countShares(6),
			'num_fed_shares_received' => $this->countEntries('share_external'),
		];
	}

	/**
	 * @return int
	 */
	protected function countUserEntries() {
		$query = $this->connection->getQueryBuilder();
		$query->selectAlias($query->createFunction('COUNT(*)'), 'num_entries')
			->from('preferences')
			->where($query->expr()->eq('configkey', $query->createNamedParameter('lastLogin')));
		$result = $query->execute();
		$row = $result->fetch();
		$result->closeCursor();

		return (int) $row['num_entries'];
	}

	/**
	 * @param string $type
	 * @return int
	 */
	protected function countStorages($type) {
		$query = $this->connection->getQueryBuilder();
		$query->selectAlias($query->createFunction('COUNT(*)'), 'num_entries')
			->from('storages');

		if ($type === 'home') {
			$query->where($query->expr()->like('id', $query->createNamedParameter('home::%')));
		} else if ($type === 'local') {
			$query->where($query->expr()->like('id', $query->createNamedParameter('local::%')));
		} else if ($type === 'other') {
			$query->where($query->expr()->notLike('id', $query->createNamedParameter('home::%')));
			$query->andWhere($query->expr()->notLike('id', $query->createNamedParameter('local::%')));
		}

		$result = $query->execute();
		$row = $result->fetch();
		$result->closeCursor();

		return (int) $row['num_entries'];
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

		return (int) $row['num_entries'];
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

		return (int) $row['num_entries'];
	}
}
