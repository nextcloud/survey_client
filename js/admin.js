$(document).ready(function() {
	var $section = $('#popularitycontestclient');
	$section.find('.popularitycontestclient_category').change(function() {
		console.log('setValue');
		OC.AppConfig.setValue(
			'popularitycontestclient',
			$(this).attr('name').substring(24),
			$(this).attr('checked') ? 'yes' : 'no'
		);
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
