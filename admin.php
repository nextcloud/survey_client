<?php
/**
 * ownCloud Workflow
 *
 * @author Joas Schilling <nickvergessen@owncloud.com>
 * @copyright 2015 ownCloud, Inc.
 *
 * This code is covered by the ownCloud Commercial License.
 *
 * You should have received a copy of the ownCloud Commercial License
 * along with this program. If not, see <https://owncloud.com/licenses/owncloud-commercial/>.
 *
 */

use OCP\Template;

\OCP\Util::addScript('popularitycontestclient', 'admin');
\OCP\Util::addStyle('popularitycontestclient', 'admin');

$application = new \OCA\PopularityContestClient\AppInfo\Application();
$collector = $application->getContainer()->query('OCA\PopularityContestClient\Collector');

$lastSentReportTime = (int) \OC::$server->getConfig()->getAppValue('popularitycontestclient', 'last_sent', 0);
if ($lastSentReportTime === 0) {
	$lastSentReportDate = \OC::$server->getL10NFactory()->get('popularitycontestclient')->t('Never');
} else {
	$lastSentReportDate = \OC::$server->getDateTimeFormatter()->formatDate($lastSentReportTime);
}

$lastReport = \OC::$server->getConfig()->getAppValue('popularitycontestclient', 'last_report', '');
if ($lastReport !== '') {
	$lastReport = json_encode(json_decode($lastReport, true), JSON_PRETTY_PRINT);
}

$template = new Template('popularitycontestclient', 'admin');
$template->assign('is_enabled', \OC::$server->getJobList()->has('OCA\PopularityContestClient\MonthlyReport', null));
$template->assign('last_sent', $lastSentReportDate);
$template->assign('last_report', $lastReport);
$template->assign('categories', $collector->getCategories());
return $template->fetchPage();
