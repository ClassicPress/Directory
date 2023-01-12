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

$tab_settings_label = PLUGIN_NAME . ' ' . esc_html__( 'Settings', 'azrcrv-smtp' );
$tab_settings       = '
<table class="form-table azrcrv-settings">
		
	<tr>
	
		<th scope="row" colspan="2">
		
			<label for="explanation">
				' . esc_html__( 'This Simple Mail Transport Protocol (SMTP) plugin will intercept the standard wp_mail and send emails via an SMTP server using PHPMAILER.', 'azrcrv-smtp' ) . '
			</label>
			
		</th>
		
	</tr>
							
	<tr>
	
		<th scope="row">
		
			<label for="smtp-host">
			
				' . esc_html__( 'SMTP Host', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="smtp-host" type="text" id="smtp-host" value="' . esc_attr( $options['smtp-host'] ) . '" class="regular-text" />
			<p class="description">' . esc_html__( 'Your mail server address.', 'azrcrv-smtp' ) . '</p>
			
		</td>
		
	</tr>
	
	<tr>
	
		<th scope="row">
		
			<label for="smtp-encryption-type">
			
				' . esc_html__( 'SMTP EncryptionType', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<select name="smtp-encryption-type">
				<option value="none" ' . ( $options['smtp-encryption-type'] == 'none' ? 'selected' : '' ) . '>None</option>
				<option value="ssl"  ' . ( $options['smtp-encryption-type'] == 'ssl' ? 'selected' : '' ) . '>SSL/TLS</option>
				<option value="tls"  ' . ( $options['smtp-encryption-type'] == 'tls' ? 'selected' : '' ) . '>StartTLS</option>
			</select>
			<p class="description">' . esc_html__( 'For most servers SSL/TLS is the recommended encryption type.', 'azrcrv-smtp' ) . '</p>
			
		</td>
		
	</tr>

	<tr>
	
		<th scope="row">
		
			<label for="smtp-port">
			
				' . esc_html__( 'SMTP Port', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="smtp-port" type="number" step="1" min="1" id="smtp-port" value="' . esc_attr( $options['smtp-port'] ) . '" class="small-text" />
			<p class="description">' . esc_html__( 'The port to your mail server (Standards are 25 for no encryption, 465 for SSL/TLS and 587 for StartTLS).', 'azrcrv-smtp' ) . '</p>
			
		</td>
		
	</tr>
	
	<tr>
	
		<th scope="row">
			<label for="smtp-username">
			
				' . esc_html__( 'SMTP Username', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="smtp-username" type="text" id="smtp-username" value="' . esc_attr( $options['smtp-username'] ) . '" class="regular-text" />
			<p class="description">' . esc_html__( 'The username to login to your mail server.', 'azrcrv-smtp' ) . '</p>
			
		</td>
		
	</tr>
	
	<tr>
	
		<th scope="row">
		
			<label for="smtp-password">
			
				' . esc_html__( 'SMTP Password', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="smtp-password" type="password" id="smtp-password" value="#ProtectedPassword#" class="regular-text" />
			<p class="description">' . esc_html__( 'The password to login to your mail server. NB. The password is stored in plain text in the database.', 'azrcrv-smtp' ) . '</p>
			
		</td>
		
	</tr>
	
	<tr>
	
		<th scope="row">
		
			<label for="allow-no-authentication">
			
				' . esc_html__( 'Allow No Authentication', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="allow-no-authentication" type="checkbox" id="allow-no-authentication" value="1" ' . checked( '1', $options['allow-no-authentication'], false ) . ' />
			<label for="allow-no-authentication"><span class="description">
				' . esc_html__( 'Allow no authentication when username not set.', 'azrcrv-smtp' ) . '
			</span></label>
			
		</td>
		
	</tr>
	
	<tr>
	
		<th scope="row">
		
			<label for="from-email-address">
			
				' . esc_html__( 'From Email Address', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="from-email-address" type="email" id="from-email-address" value="' . esc_attr( $options['from-email-address'] ) . '" class="regular-text" />
			<p class="description">' . esc_html__( 'This will be used as the "From" email address; leave blank to use the admin email.', 'azrcrv-smtp' ) . '</p>
			
		</td>
		
	</tr>
	
	<tr>
	
		<th scope="row">
		
			<label for="from-email-name">
			
				' . esc_html__( 'From Email Name', 'azrcrv-smtp' ) . '
				
			</label>
			
		</th>
		
		<td>
		
			<input name="from-email-name" type="text" id="from-email-name" value="' . esc_attr( $options['from-email-name'] ) . '" class="regular-text" />
			<p class="description">' . sprintf( esc_html__( 'This will be used as the name for the "From" email address; leave blank to use %s.', 'azrcrv-smtp' ), 'ClassicPress' ) . '</p>
			
		</td>
		
	</tr>

</table>';
