<?php
/*
	other plugins tab on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\SMTP;

/**
 * Instructions tab.
 */
$tab_instructions_label = esc_html__( 'Instructions', 'azrcrv-smtp' );
$tab_instructions       = '
<table class="form-table azrcrv-settings">
	
	<tr>
	
		<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
			
				<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'SMTP Settings', 'azrcrv-smtp' ) . '</h2>
			
		</th>

	</tr>

	<tr>
	
		<td scope="row" colspan=2>
		
			<p>' .
				sprintf( esc_html__( 'Simply configure the settings on the %s Settings tab and email will automatically be intercepted and sent by PHPMailer using the supplied SMTP server details.', 'azrcrv-smtp' ), PLUGIN_NAME ) . '
					
			</p>
			
		</td>
	
	</tr>
	
	<tr>
	
		<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
			
				<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'Test Email', 'azrcrv-smtp' ) . '</h2>
			
		</th>

	</tr>

	<tr>
	
		<td scope="row" colspan=2>
		
			<p>' .
				esc_html__( 'You can use the Test Email tab to check if the SMTP details are correct and that sending of emails is working correctly.', 'azrcrv-smtp' ) . '
					
			</p>
			
		</td>
	
	</tr>
	
</table>';
