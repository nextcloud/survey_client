<?php
/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */


namespace OCA\Survey_Client\Settings;

use OCA\Survey_Client\BackgroundJobs\MonthlyReport;
use OCA\Survey_Client\Collector;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IAppConfig;
use OCP\BackgroundJob\IJobList;
use OCP\IConfig;
use OCP\IDateTimeFormatter;
use OCP\IL10N;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {
	public function __construct(
		protected Collector $collector,
		protected IConfig $config,
		protected IAppConfig $appConfig,
		protected IL10N $l,
		protected IDateTimeFormatter $dateTimeFormatter,
		protected IJobList $jobList,
	) {
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$lastSentReportTime = $this->appConfig->getAppValueInt('last_sent');
		if ($lastSentReportTime === 0) {
			$lastSentReportDate = $this->l->t('Never');
		} else {
			$lastSentReportDate = $this->dateTimeFormatter->formatDate($lastSentReportTime);
		}

		$lastReport = $this->appConfig->getAppValueString('last_report', lazy: true);
		if ($lastReport !== '') {
			$lastReport = json_encode(json_decode($lastReport, true), JSON_PRETTY_PRINT);
		}

		$parameters = [
			'is_enabled' => $this->jobList->has(MonthlyReport::class, null),
			'last_sent' => $lastSentReportDate,
			'last_report' => $lastReport,
			'categories' => $this->collector->getCategories()
		];

		return new TemplateResponse('survey_client', 'admin', $parameters);
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection(): string {
		return 'survey_client';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 *             the admin section. The forms are arranged in ascending order of the
	 *             priority values. It is required to return a value between 0 and 100.
	 */
	public function getPriority(): int {
		return 50;
	}
}
