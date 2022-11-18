<?php
/*
	tab output on azurecurve page
*/
?>

<div id="tabs" class="azrcrv-ui-tabs">
	<ul class="azrcrv-ui-tabs-nav azrcrv-ui-widget-header" role="tablist">
		<li class="azrcrv-ui-state-default azrcrv-ui-state-active" aria-controls="tab-panel-active" aria-labelledby="tab-active" aria-selected="true" aria-expanded="true" role="tab">
			<?php // phpcs:ignore. ?>
			<a id="tab-active" class="azrcrv-ui-tabs-anchor" href="#tab-panel-active"><?php echo $tab_active_label; ?></a>
		</li>
		<li class="azrcrv-ui-state-default" aria-controls="tab-panel-plugins" aria-labelledby="tab-other" aria-selected="false" aria-expanded="false" role="tab">
			<?php // phpcs:ignore. ?>
			<a id="tab-other" class="azrcrv-ui-tabs-anchor" href="#tab-panel-other"><?php echo $tab_other_label; ?></a>
		</li>
	</ul>
	<div id="tab-panel-active" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs" role="tabpanel" aria-hidden="false">
			<legend class='screen-reader-text'>
				<?php
				// phpcs:ignore.
				echo $tab_active_label;
				?>
			</legend>
			<?php
			// phpcs:ignore.
			echo $tab_active;
			?>
	</div>
	<div id="tab-panel-other" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
			<legend class='screen-reader-text'>
				<?php
				// phpcs:ignore.
				echo $tab_other_label;
				?>
			</legend>
			<?php
			// phpcs:ignore.
			echo $tab_other;
			?>
	</div>
</div>
<?php
/*
	donate button on settings page
*/
?>
<div class='azrcrv-donate'>
	<?php
		printf( esc_html__( 'Support %s', 'azrcrv-smtp' ), esc_html( DEVELOPER_NAME ) );
	?>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="MCJQN9SJZYLWJ">
		<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
	</form>
	<span>
		<?php
		esc_html_e( 'You can help support the development of our free plugins by donating a small amount of money.', 'azrcrv-smtp' );
		?>
	</span>
</div>
