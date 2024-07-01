<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

return [
	'ocs' => [
		['name' => 'Endpoint#sendReport', 'url' => '/api/v1/report', 'verb' => 'POST'],
		['name' => 'Endpoint#enableMonthly', 'url' => '/api/v1/monthly', 'verb' => 'POST'],
		['name' => 'Endpoint#disableMonthly', 'url' => '/api/v1/monthly', 'verb' => 'DELETE'],
	],
];
