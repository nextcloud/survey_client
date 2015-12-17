<?php
/** @var $l \OCP\IL10N */
/** @var $_ array */
?>
<div id="popularitycontestclient" class="section">
	<h2><?php p($l->t('Usage report')); ?></h2>

	<p>
		<?php p($l->t('When the server receives a new report of your instance, all entries from previous reports are removed. So in case you disable one of the settings below, you can send a new report, to delete the data, that is currently stored on the server.')); ?>
	</p>

	<button><?php p($l->t('Send new report now')); ?></button>

	<p>
		<input id="popularitycontestclient_monthly_report" name="popularitycontestclient_monthly_report"
			   type="checkbox" class="checkbox" value="1" <?php if ($_['is_enabled']): ?> checked="checked"<?php endif; ?> />
		<label for="popularitycontestclient_monthly_report"><?php p($l->t('Send "Usage report" monthly')); ?></label>
	</p>

	<h3><?php p($l->t('Data Control')); ?></h3>
	<?php
	foreach ($_['categories'] as $category => $data) {
		?>
		<p>
			<input id="popularitycontestclient_<?php p($category); ?>" name="popularitycontestclient_<?php p($category); ?>"
				   type="checkbox" class="checkbox popularitycontestclient_category" value="1" <?php if ($data['enabled']): ?> checked="checked"<?php endif; ?> />
			<label for="popularitycontestclient_<?php p($category); ?>"><?php print_unescaped($data['displayName']); ?></label>
		</p>
		<?php
	}
	?>

	<h3><?php p($l->t('Last report')); ?></h3>

	<p><textarea title="<?php p($l->t('Last report')); ?>" class="last_report" readonly="readonly"><?php p($_['last_report']);?></textarea></p>

	<em class="last_sent"><?php p($l->t('Sent on: %s', [$_['last_sent']])); ?></em>
</div>
