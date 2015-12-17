$(document).ready(function() {
	var $section = $('#popularitycontestclient');
	$section.find('.popularitycontestclient_category').change(function() {
		var $button = $(this);
		$button.attr('disabled', true);

		OC.AppConfig.postCall('setValue', {
			app: 'popularitycontestclient',
			key: $(this).attr('name').substring(24),
			value: $(this).attr('checked') ? 'yes' : 'no'
		}, function() {
			$button.attr('disabled', false);
		});
	});

	$section.find('#popularitycontestclient_monthly_report').change(function() {
		var $button = $(this);
		$button.attr('disabled', true);

		$.ajax({
			url: OC.linkToOCS('apps/popularitycontestclient/api/v1/', 2) + 'monthly?format=json',
			type: $(this).attr('checked') ? 'POST' : 'DELETE',
			success: function() {
				$button.attr('disabled', false);
			}
		});
	});

	$section.find('button').click(function() {
		var $button = $(this);
		$button.attr('disabled', true);
		$.ajax({
			url: OC.linkToOCS('apps/popularitycontestclient/api/v1/', 2) + 'report?format=json',
			type: 'POST',
			success: function(response) {
				$button.attr('disabled', false);

				$section.find('.last_report').text(JSON.stringify(response.ocs.data, undefined, 4));
				$section.find('.last_sent').text(t('popularitycontestclient', 'Sent on: {on}', {
					on: moment().format('LL')
				}));
			},
			error: function(xhr) {
				$button.attr('disabled', false);
				OC.Notification.showTemporary(t('popularitycontestclient', 'An error occurred while sending your report.'));

				var response = xhr.responseJSON;
				$section.find('.last_report').text(JSON.stringify(response.ocs.data, undefined, 4));
			}
		});
	});
});
