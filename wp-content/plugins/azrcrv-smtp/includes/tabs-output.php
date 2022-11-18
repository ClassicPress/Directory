<?php
/*
	tab output on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\SMTP;

/**
 * Output tabs.
 */
?>

<?php
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['test-email'] ) ) {
	$tab1active     = '';
	$tab2active     = 'azrcrv-ui-state-active';
	$tab1visibility = 'azrcrv-ui-tabs-hidden';
	$tab2visibility = '';
	$tab1aria       = 'true';
	$tab2aria       = 'false';
} else {
	$tab1active     = 'azrcrv-ui-state-active';
	$tab2active     = '';
	$tab1visibility = '';
	$tab2visibility = 'azrcrv-ui-tabs-hidden';
	$tab1aria       = 'false';
	$tab2aria       = 'true';
}
?>


<div id="tabs" class="azrcrv-ui-tabs">
	<ul class="azrcrv-ui-tabs-nav azrcrv-ui-widget-header" role="tablist">
		<?php // phpcs:ignore. ?>
		<li class="azrcrv-ui-state-default <?php echo $tab1active; ?>" aria-controls="tab-panel-settings" aria-labelledby="tab-settings" aria-selected="<?php $tab1aria; ?>" aria-expanded="<?php $tab1aria; ?>" role="tab">
			<?php // phpcs:ignore. ?>
			<a id="tab-settings" class="azrcrv-ui-tabs-anchor" href="#tab-panel-settings"><?php echo $tab_settings_label; ?></a>
		</li>
		<?php // phpcs:ignore. ?>
		<li class="azrcrv-ui-state-default <?php echo $tab2active; ?>" aria-controls="tab-panel-test" aria-labelledby="tab-test" aria-selected="<?php $tab2aria; ?>" aria-expanded="<?php $tab2aria; ?>" role="tab">
			<?php // phpcs:ignore. ?>
			<a id="tab-test" class="azrcrv-ui-tabs-anchor" href="#tab-panel-test"><?php echo $tab_test_label; ?></a>
		</li>
		<li class="azrcrv-ui-state-default" aria-controls="tab-panel-instructions" aria-labelledby="tab-instructions" aria-selected="false" aria-expanded="false" role="tab">
			<?php // phpcs:ignore. ?>
			<a id="tab-instructions" class="azrcrv-ui-tabs-anchor" href="#tab-panel-instructions"><?php echo $tab_instructions_label; ?></a>
		</li>
		<li class="azrcrv-ui-state-default" aria-controls="tab-panel-plugins" aria-labelledby="tab-plugins" aria-selected="false" aria-expanded="false" role="tab">
			<?php // phpcs:ignore. ?>
			<a id="tab-plugins" class="azrcrv-ui-tabs-anchor" href="#tab-panel-plugins"><?php echo $tab_plugins_label; ?></a>
		</li>
	</ul>
	<?php // phpcs:ignore. ?>
	<div id="tab-panel-settings" class="azrcrv-ui-tabs-scroll <?php echo $tab1visibility; ?>" role="tabpanel" aria-hidden="false">
		<form method="post" action="admin-post.php">

			<input type="hidden" name="action" value="<?php echo esc_attr( PLUGIN_UNDERSCORE ); ?>_save_options" />

			<?php
			// <!-- Adding security through hidden referer field -->.
			wp_nonce_field( PLUGIN_HYPHEN, PLUGIN_HYPHEN . '-nonce' );
			?>
			
			<fieldset>
				<legend class='screen-reader-text'>
					<?php
					// phpcs:ignore.
					echo $tab_settings_label;
					?>
				</legend>
				<?php
				// phpcs:ignore.
				echo $tab_settings;
				?>
			</fieldset>

			<input type="submit" name="btn_save" value="<?php esc_html_e( 'Save Settings', 'azrcrv-smtp' ); ?>" class="button-primary"/>
		</form>
	</div>
	<?php // phpcs:ignore. ?>
	<div id="tab-panel-test" class="azrcrv-ui-tabs-scroll <?php echo $tab2visibility; ?>" role="tabpanel" aria-hidden="false">
		<form method="post" action="admin-post.php">

			<input type="hidden" name="action" value="<?php echo esc_attr( PLUGIN_UNDERSCORE ); ?>_send_test_email" />

			<?php
			// <!-- Adding security through hidden referer field -->.
			wp_nonce_field( PLUGIN_HYPHEN . '-send-test-email', PLUGIN_HYPHEN . '-send-test-email-nonce' );
			?>
			
			<fieldset>
				<legend class='screen-reader-text'>
					<?php
					// phpcs:ignore.
					echo $tab_test_label;
					?>
				</legend>
				<?php
				// phpcs:ignore.
				echo $tab_test;
				?>
			</fieldset>

			<input type="submit" name="btn_save" value="<?php esc_html_e( 'Send Test Email', 'azrcrv-smtp' ); ?>" class="button-primary"/>
		</form>
	</div>
	<div id="tab-panel-instructions" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
		<fieldset>
			<legend class='screen-reader-text'>
				<?php
				// phpcs:ignore.
				echo $tab_instructions_label;
				?>
			</legend>
			<?php
			// phpcs:ignore.
			echo $tab_instructions;
			?>
		</fieldset>
	</div>
	<div id="tab-panel-plugins" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
		<fieldset>
			<legend class='screen-reader-text'>
				<?php
				// phpcs:ignore.
				echo $tab_plugins_label;
				?>
			</legend>
			<?php
			// phpcs:ignore.
			echo $tab_plugins;
			?>
		</fieldset>
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
