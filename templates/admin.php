<?php
/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2015 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

/** @var \OCP\IL10N $l */
/** @var array $_ */

script('survey_client', 'admin');
style('survey_client', 'admin');
?>

<div id="survey_client" class="section">
	<h2><?php p($l->t('Usage survey')); ?></h2>

	<p>
		<?php p($l->t('You can help improve Nextcloud by sending us some data about your current setup and usage.')); ?>
	</p>

	<p>
		<?php p($l->t('We take your privacy seriously. Sending data is disabled by default, and should you choose to turn it on, it will be anonymized first, and you are given the option of what things to share. Upon receiving a report, the previous one is removed. To delete the stored usage data, upload an empty report by unchecking all of the boxes then sending a new report.')); ?>
	</p>

	<button><?php p($l->t('Send new report now')); ?></button>

	<p>
		<input id="survey_client_monthly_report" name="survey_client_monthly_report"
			   type="checkbox" class="checkbox" value="1" <?php if ($_['is_enabled']): ?> checked="checked"<?php endif; ?> />
		<label for="survey_client_monthly_report"><?php p($l->t('Send usage survey monthly')); ?></label>
	</p>

	<h3><?php p($l->t('Data to send')); ?></h3>
	<?php
	foreach ($_['categories'] as $category => $data) {
		?>
		<p>
			<input id="survey_client_<?php p($category); ?>" name="survey_client_<?php p($category); ?>"
				   type="checkbox" class="checkbox survey_client_category" value="1" <?php if ($data['enabled']): ?> checked="checked"<?php endif; ?> />
			<label for="survey_client_<?php p($category); ?>"><?php print_unescaped($data['displayName']); ?></label>
		</p>
		<?php
	}
?>

	<div id="last_report">
		<h3>
			<span class="icon icon-triangle-n icon-triangle-s"></span>
			<?php p($l->t('Last report sent on: %s', [$_['last_sent']])); ?>
		</h3>
		<p class="hidden"><textarea title="<?php p($l->t('Last report')); ?>" class="last_report" readonly="readonly"><?php p($_['last_report']);?></textarea></p>
	</div>

</div>
