<?php
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
		return 'apps';
	}

	/**
	 * @return string
	 */
	public function getDisplayName() {
		return $this->l->t('App list <em>(for each app: name, version, enabled status)</em>');
	}

	/**
	 * @return array (string => string|int)
	 */
	public function getData() {
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
