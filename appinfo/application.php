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

namespace OCA\PopularityContestClient\AppInfo;

use OCA\PopularityContestClient\Controller\EndpointController;
use OCA\PopularityContestClient\Collector;
use OCP\AppFramework\App;
use OCP\IContainer;

class Application extends App {
	public function __construct (array $urlParams = array()) {
		parent::__construct('popularitycontestclient', $urlParams);
		$container = $this->getContainer();

		$container->registerService('EndpointController', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');

			return new EndpointController(
				$c->query('AppName'),
				$server->getRequest(),
				$c->query('OCA\PopularityContestClient\Collector'),
				$server->getHTTPClientService()
			);
		});

		$container->registerService('OCA\PopularityContestClient\Collector', function(IContainer $c) {
			/** @var \OCP\IServerContainer $server */
			$server = $c->query('ServerContainer');

			return new Collector(
				$server->getConfig(),
				$server->getDatabaseConnection(),
				$server->getIniWrapper(),
				$server->getL10NFactory()->get('popularitycontestclient')
			);
		});

		$container->registerCapability('Capabilities');
	}
}
