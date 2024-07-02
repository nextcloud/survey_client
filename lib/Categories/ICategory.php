<?php
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Survey_Client\Categories;

/**
 * Interface ICategory
 *
 * TODO Move to core public API?
 *
 * @package OCA\Survey_Client\Categories
 */
interface ICategory {
	/**
	 * @return string
	 */
	public function getCategory();

	/**
	 * @return string
	 */
	public function getDisplayName();

	/**
	 * @return array (string => string|int)
	 */
	public function getData();
}
