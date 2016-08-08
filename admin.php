<?php
/**
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
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

use OCP\Template;

\OCP\Util::addScript('survey_client', 'admin');
\OCP\Util::addStyle('survey_client', 'admin');

$application = new \OCA\Survey_Client\AppInfo\Application();
$collector = $application->getContainer()->query('OCA\Survey_Client\Collector');

$lastSentReportTime = (int) \OC::$server->getConfig()->getAppValue('survey_client', 'last_sent', 0);
if ($lastSentReportTime === 0) {
	$lastSentReportDate = \OC::$server->getL10NFactory()->get('survey_client')->t('Never');
} else {
	$lastSentReportDate = \OC::$server->getDateTimeFormatter()->formatDate($lastSentReportTime);
}

$lastReport = \OC::$server->getConfig()->getAppValue('survey_client', 'last_report', '');
if ($lastReport !== '') {
	$lastReport = json_encode(json_decode($lastReport, true), JSON_PRETTY_PRINT);
}

$template = new Template('survey_client', 'admin');
$template->assign('is_enabled', \OC::$server->getJobList()->has('OCA\Survey_Client\BackgroundJobs\MonthlyReport', null));
$template->assign('last_sent', $lastSentReportDate);
$template->assign('last_report', $lastReport);
$template->assign('categories', $collector->getCategories());
return $template->fetchPage();
