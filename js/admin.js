/*
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-only
 */
document.addEventListener('DOMContentLoaded', function() {
	const section = document.getElementById('survey_client');
	if (!section) {
		return;
	}

	function parseJsonResponse(xhr) {
		if (xhr.responseType === 'json' && xhr.response) {
			return xhr.response;
		}

		if (!xhr.responseText) {
			return null;
		}

		try {
			return JSON.parse(xhr.responseText);
		} catch (e) {
			return null;
		}
	}

	function sendOcsRequest(url, method, onSuccess, onError) {
		const xhr = new XMLHttpRequest();
		xhr.open(method, url, true);
		xhr.responseType = 'json';
		xhr.setRequestHeader('Accept', 'application/json');
		xhr.setRequestHeader('OCS-APIRequest', 'true');
		if (typeof OC !== 'undefined' && OC.requestToken) {
			xhr.setRequestHeader('requesttoken', OC.requestToken);
		}

		xhr.onload = function() {
			if (xhr.status >= 200 && xhr.status < 300) {
				onSuccess(parseJsonResponse(xhr));
				return;
			}

			onError(xhr, parseJsonResponse(xhr));
		};

		xhr.onerror = function() {
			onError(xhr, parseJsonResponse(xhr));
		};

		xhr.send();
	}

	function setReportContent(reportElement, content) {
		reportElement.textContent = content;
		if ('value' in reportElement) {
			reportElement.value = content;
		}
	}

	Array.prototype.forEach.call(section.querySelectorAll('.survey_client_category'), function(checkbox) {
		checkbox.addEventListener('change', function() {
			checkbox.disabled = true;

			OCP.AppConfig.setValue(
				'survey_client',
				checkbox.name.substring(14),
				checkbox.checked ? 'yes' : 'no',
				{
					success: function() {
						checkbox.disabled = false;
					},
					error: function() {
						checkbox.disabled = false;
					}
				}
			);
		});
	});

	const monthlyReportCheckbox = section.querySelector('#survey_client_monthly_report');
	if (monthlyReportCheckbox) {
		monthlyReportCheckbox.addEventListener('change', function() {
			monthlyReportCheckbox.disabled = true;

			sendOcsRequest(
				OC.linkToOCS('apps/survey_client/api/v1', 2) + 'monthly?format=json',
				monthlyReportCheckbox.checked ? 'POST' : 'DELETE',
				function() {
					monthlyReportCheckbox.disabled = false;
				},
				function() {
					monthlyReportCheckbox.disabled = false;
				}
			);
		});
	}

	const reportButton = section.querySelector('button');
	if (reportButton) {
		reportButton.addEventListener('click', function() {
			reportButton.disabled = true;

			sendOcsRequest(
				OC.linkToOCS('apps/survey_client/api/v1', 2) + 'report?format=json',
				'POST',
				function(response) {
					reportButton.disabled = false;

					const reportElement = section.querySelector('.last_report');
					if (reportElement && response && response.ocs && response.ocs.data) {
						setReportContent(reportElement, JSON.stringify(response.ocs.data, undefined, 4));
					}

					const lastSentElement = section.querySelector('.last_sent');
					if (lastSentElement) {
						lastSentElement.textContent = t('survey_client', 'Last report sent on: {on}', {
							on: moment().format('LL')
						});
					}

					if (reportElement && reportElement.closest('div')) {
						reportElement.closest('div').classList.remove('empty');
					}
				},
				function(xhr, response) {
					reportButton.disabled = false;
					OCP.Toast.error(t('survey_client', 'An error occurred while sending your report.'));

					const reportElement = section.querySelector('.last_report');
					if (reportElement && response && response.ocs && response.ocs.data) {
						setReportContent(reportElement, JSON.stringify(response.ocs.data, undefined, 4));
					}
				}
			);
		});
	}

	const lastReportHeader = document.querySelector('#last_report h3');
	if (lastReportHeader) {
		lastReportHeader.addEventListener('click', function() {
			const parent = lastReportHeader.parentElement;
			if (parent && !parent.classList.contains('empty')) {
				const content = parent.querySelector('p');
				if (content) {
					content.classList.toggle('hidden');
				}

				const icon = lastReportHeader.querySelector('.icon');
				if (icon) {
					icon.classList.toggle('icon-triangle-s');
				}
			}
		});
	}
});
