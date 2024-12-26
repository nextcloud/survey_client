<?php
/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */


namespace OCA\Survey_Client\Settings;

use OCA\Survey_Client\Collector;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\BackgroundJob\IJobList;
use OCP\IConfig;
use OCP\IDateTimeFormatter;
use OCP\IL10N;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {

	/** @var Collector */
	private $collector;

	/** @var IConfig */
	private $config;

	/** @var IL10N */
	private $l;

	/** @var IDateTimeFormatter */
	private $dateTimeFormatter;

	/** @var IJobList */
	private $jobList;

	public function __construct(Collector $collector,
		IConfig $config,
		IL10N $l,
		IDateTimeFormatter $dateTimeFormatter,
		IJobList $jobList,
	) {
		$this->collector = $collector;
		$this->config = $config;
		$this->l = $l;
		$this->dateTimeFormatter = $dateTimeFormatter;
		$this->jobList = $jobList;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {
		$lastSentReportTime = (int)$this->config->getAppValue('survey_client', 'last_sent', '0');
		if ($lastSentReportTime === 0) {
			$lastSentReportDate = $this->l->t('Never');
		} else {
			$lastSentReportDate = $this->dateTimeFormatter->formatDate($lastSentReportTime);
		}

		$lastReport = $this->config->getAppValue('survey_client', 'last_report', '');
		if ($lastReport !== '') {
			$lastReport = json_encode(json_decode($lastReport, true), JSON_PRETTY_PRINT);
		}

		$parameters = [
			'is_enabled' => $this->jobList->has('OCA\Survey_Client\BackgroundJobs\MonthlyReport', null),
			'last_sent' => $lastSentReportDate,
			'last_report' => $lastReport,
			'categories' => $this->collector->getCategories()
		];

		return new TemplateResponse('survey_client', 'admin', $parameters);
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection() {
		return 'survey_client';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 *             the admin section. The forms are arranged in ascending order of the
	 *             priority values. It is required to return a value between 0 and 100.
	 */
	public function getPriority() {
		return 50;
	}
}
