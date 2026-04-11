<?php

declare(strict_types=1);

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
	public function getCategory(): string;

	public function getDisplayName(): string;

	/**
	 * @return array<string, string|int>
	 */
	public function getData(): array;
}
