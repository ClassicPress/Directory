<?php
/*
	other plugins tab on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\SMTP;

/**
 * Settings tab.
 */

$tab_test_label = esc_html__( 'Test Email', 'azrcrv-smtp' );
$tab_test       = '
<table class="form-table azrcrv-settings">
		
	<tr>
	
		<th scope="row" colspan="2">
		
			<label for="explanation">
				' . esc_html__( 'Test your email configuration by sending a test email.', 'azrcrv-smtp' ) . '
			</label>
			
		</th>
		
	</tr>
	
	<tr>
	
		<th scope="row">
		
			<label for="test-email-address">
			
				' . esc_html__( 'Email Address', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="test-email-address" type="email" id="test-email-address" value="' . esc_attr( $options['test-email-address'] ) . '" class="regular-text" />
			
		</td>
		
	</tr>
	
	<tr>
	
		<th scope="row">
		
			<label for="test-email-subject">
			
				' . esc_html__( 'Email Subject', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="test-email-subject" type="text" id="test-email-subject" value="' . esc_attr( $options['test-email-subject'] ) . '" class="regular-text" />
			
		</td>
		
	</tr>
	
	<tr>
	
		<th scope="row">
		
			<label for="test-email-message">
			
				' . esc_html__( 'Email Message', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="test-email-message" type="text" id="test-email-message" value="' . esc_attr( $options['test-email-message'] ) . '" class="regular-text" />
			
		</td>
		
	</tr>

</table>';
