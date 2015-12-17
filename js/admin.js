$(document).ready(function() {
	$('.popularitycontestclient_category').change(function() {
		OC.AppConfig.setValue(
			'popularitycontestclient',
			$(this).attr('name').substring(24),
			$(this).attr('checked') ? 'yes' : 'no'
		);
	});
});
