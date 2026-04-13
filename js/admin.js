/*
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-only
 */
document.addEventListener('DOMContentLoaded', function() {
	/**
	 * Copied from @nextcloud/l10n
	 */
	function getLanguage() {
		return (typeof document !== 'undefined' && document.documentElement.lang)
			|| (window.navigator?.language ?? 'en')
	}

	const section = document.getElementById('survey_client');

	// Handle survey client category changes
	const categoryElements = section.querySelectorAll('.survey_client_category');
	categoryElements.forEach(function(element) {
		element.addEventListener('change', function() {
			this.disabled = true;

			OCP.AppConfig.setValue(
				'survey_client',
				this.name.substring(14),
				this.checked ? 'yes' : 'no',
				{
					success: () => {
						this.disabled = false;
					}
				}
			);
		});
	});

	// Handle monthly report toggle
	const monthlyReportElement = document.getElementById('survey_client_monthly_report');
	monthlyReportElement.addEventListener('change', function() {
		this.disabled = true;
		const method = this.checked ? 'POST' : 'DELETE';

		fetch(OC.linkToOCS('apps/survey_client/api/v1', 2) + 'monthly?format=json', {
			method: method,
			headers: {
				requestToken: OC.requestToken,
			},
		})
			.then(() => {
				this.disabled = false;
			})
			.catch(() => {
				this.disabled = false;
			});
	});

	// Handle report button click
	const sendNowButton = document.getElementById('send_report_now');
	sendNowButton.addEventListener('click', function() {
		this.disabled = true;

		fetch(OC.linkToOCS('apps/survey_client/api/v1', 2) + 'report?format=json', {
			method: 'POST',
			headers: {
				requestToken: OC.requestToken,
			},
		})
			.then(response => {
				if (!response.ok) {
					throw new Error('Request failed with status ' + response.status);
				}
				return response.json();
			})
			.then(response => {
				this.disabled = false;

				const lastReportElement = section.querySelector('.last_report');

				lastReportElement.textContent = JSON.stringify(response.ocs.data, undefined, 4);

				const lastSentElement = section.querySelector('summary');
				lastSentElement.textContent = t('survey_client', 'Last report sent on: {on}', {
					on: (new Date()).toLocaleString(getLanguage(), { dateStyle: 'long', timeStyle: 'short' })
				});

				// Unfold the details element
				const lastSentDetails = document.getElementById('last_report');
				lastSentDetails.open = true;
			})
			.catch((error) => {
				console.error(error)
				this.disabled = false;
				OCP.Toast.error(t('survey_client', 'An error occurred while sending your report.'));
			});
	});
});
