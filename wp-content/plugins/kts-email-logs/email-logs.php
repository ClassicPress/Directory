<?php
/**
 * Plugin Name: Email Logs
 * Plugin URI: https://timkaye.org
 * Description: Stores email logs in a custom database table
 * Version: 0.1.2
 * Author: Tim Kaye
 * Author URI: https://timkaye.org
 * Text Domain: kts_email_logs
*/

/* INCLUDE REQUIRED FILES */
require_once __DIR__ . '/inc/class-list-table.php'; // extends the WP_List_Table class
require_once __DIR__ . '/inc/database.php'; // creates custom database table
require_once __DIR__ . '/inc/settings.php'; // enables changes of settings for this plugin

/* ENABLE UPDATING MECHANISM */
require_once __DIR__ . '/inc/UpdateClient.class.php';

/* FIRE HOOK FOR DATABASE CREATION */
register_activation_hook( __FILE__, 'kts_email_logs_create_db' );

# SEND DATA TO EMAIL LOGS
function kts_send_data_to_email_logs_on_success( $args ) { // $mail_data
	global $wpdb, $kts_email_id;
	$table_name = $wpdb->prefix . 'kts_email_logs';

	$user = get_user_by( 'email', $args['to'] );
	$headers = is_array( $args['headers'] ) ? array_map( 'sanitize_text_field', $args['headers'] ) : [sanitize_text_field( $args['headers'] )];
	$attachments = is_array( $args['attachments'] ) ? array_map( 'esc_url_raw', $args['attachments'] ) : [esc_url_raw( $args['attachments'] )];

	$email_array = array(
		'status'				=> 1,
		'recipient'			=> sanitize_text_field( $user->display_name ),
		'email'				=> sanitize_email( $args['to'] ),
		'subject'			=> sanitize_text_field( $args['subject'] ),
		'message'			=> esc_html( preg_replace('~<script[^>]*>.*</script\s*>~is', '', ( $args['message'] ) ) ),
		'sent'				=> time(),
		'headers'			=> maybe_serialize( $headers ),
		'attachments'	=> maybe_serialize( $attachments )
	);

	$wpdb->insert( $table_name, $email_array ); // $wpdb->insert sanitizes data

	$kts_email_id = $wpdb->insert_id;

	return $args;
}
add_filter( 'wp_mail', 'kts_send_data_to_email_logs_on_success' );
// add_action( 'wp_mail_succeeded', 'kts_send_data_to_email_logs_on_success' ); // WP


function kts_send_data_to_email_logs_on_failure( $error ) {

	global $wpdb, $kts_email_id;
	$table_name = $wpdb->prefix . 'kts_email_logs';

	$error_message = $error->errors['wp_mail_failed'][0];

	$email = $error->error_data['wp_mail_failed']['to'][0];
	$user = get_user_by( 'email', $email );

	//waiting for 'wp_mail_succeeded' hook to be added to CP
	//$headers = is_array( $args['headers'] ) ? array_map( 'sanitize_text_field', $args['headers'] ) : [sanitize_text_field( $args['headers'] )];
	//$attachments = is_array( $args['attachments'] ) ? array_map( 'esc_url_raw', $args['attachments'] ) : [esc_url_raw( $args['attachments'] )];

	$email_array = array(
		//'message_id'=> $kts_email_id,
		'status'	=> 0,
		//'recipient'	=> sanitize_text_field( $user->display_name ),
		//'email'		=> sanitize_email( $email ),
		//'subject'	=> sanitize_text_field( $error->error_data['wp_mail_failed']['subject'] ),
		//'message'	=> wp_filter_post_kses( $error->error_data['wp_mail_failed']['message'] ),
		//'sent'		=> time(),		
		//'headers'		=> maybe_serialize( $headers ),
		//'attachments'	=> maybe_serialize( $attachments ),
		'error'		=> $error_message,
		'exception'	=> $error->error_data['wp_mail_failed']['phpmailer_exception_code']
	);

	$where = ['message_id' => $kts_email_id];

	//$wpdb->insert( $table_name, $email_array ); // $wpdb->insert sanitizes data
	$wpdb->update( $table_name, $email_array, $where ); // $wpdb->update sanitizes data
}
add_action( 'wp_mail_failed', 'kts_send_data_to_email_logs_on_failure' );


# RETRIEVE DATA FROM EMAIL LOGS
function kts_set_screen_email_logs( $status, $option, $value ) {
	return $value;
}
add_filter( 'set-screen-option', 'kts_set_screen_email_logs', 10, 3 );

function kts_menu_email_logs() {

	$hook = add_submenu_page(
		'tools.php',
		'Email Logs',
		'Email Logs',
		'manage_options',
		'email-logs',
		'kts_list_email_logs'
	);

	add_action( 'load-' . $hook, 'kts_screen_option_email_logs' );
	add_action( 'load-' . $hook, 'kts_csv_email_logs' );
}
add_action( 'admin_menu', 'kts_menu_email_logs' );

function kts_list_email_logs() {
	$logs = new KTS_Email_Logs();
	$logs->prepare_items();

	$real_nonce = cp_set_nonce( 'real_kts_email_nonce' );
	$nonce = $real_nonce['name'] . '-' . $real_nonce['value'];
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Email Logs</h1>
		<hr class="wp-header-end">
		<h2 class="screen-reader-text">Filter list of email logs</h2>
		<?php
		$logs->views();
		?>

		<form id="email-logs-filter" method="get">
			<?php
			$logs->search_box( 'Search Logs', 'logs' );
			
			/*
			 * Ensures the current page reloads after bulk action or search submitted
			 * 
			 * Also submits true nonce with form using Real Nonce 
			 */
			?>
			<input type="hidden" name="page" value="email-logs">
			<input type="hidden" name="real_nonce" value="<?php echo $nonce; ?>">
			<?php
			$logs->display();
			?>
		</form>

		<br class="clear">
	</div>
<?php
}


# SCREEN OPTIONS
function kts_screen_option_email_logs() {

	$option = 'per_page';
	$args   = array(
		'label'   => 'Logs',
		'default' => 20,
		'option'  => 'logs_per_page'
	);

	add_screen_option( $option, $args );

	$logs = new KTS_Email_Logs();
}


# CSV EXPORT OF ALL OR MULTIPLE SELECTED ERROR LOGS
function kts_csv_email_logs() {

	# Bail if no action set
	if ( empty( $_GET['action'] ) ) {
		return;
	}

	# Bail if the action is neither of these
	if ( ! in_array( $_GET['action'], ['export-all-logs', 'bulk-export'] ) ) {
		return;
	}

	# Bail if the action is bulk-export, but no IDs have been post in the $_GET variable
	if ( $_GET['action'] === 'bulk-export' && empty( $_GET['email_logs'] ) ) {
		return;
	}

	# Verify true nonce using Real Nonce
	$real_nonce = sanitize_key( $_GET['real_nonce'] );
	$exploded = $check_nonce = false;
	if ( strpos( $real_nonce, '-' ) !== false ) {
		$exploded = explode( '-', $real_nonce );
		$check_nonce = cp_check_nonce( $exploded[0], $exploded[1] );
	}

	if ( in_array( false, [$exploded, $check_nonce] ) ) {
		echo '<div class="notice notice-error is-dismissible"><p>That action is not possible without a suitable nonce.</p></div>';
		return;
	}

	# Get specified logs
	$logs = [];
	if ( $_GET['action'] === 'export-all-logs' ) {
		$logs = KTS_Email_Logs::get_all_logs();
	}
	else {
		$ids = array_map( 'absint', $_GET['email_logs'] );
		$logs = KTS_Email_Logs::get_selected_logs( $ids );
	}

	# Just in case logs are empty, bail out
	if ( empty( $logs) ) {
		return;
	}

	# Set details for the redirect to force download and name of CSV file
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ); 
	header( 'Content-Description: File Transfer' ); 
	header( 'Content-type: text/csv' );
	header( 'Content-Disposition: attachment; filename="email-error-logs-' . date( 'Y-m-d' ) . '.csv"' );
	header( 'Pragma: public' );
	header( 'Expires: 0' );

	# Create the CSV file
	$timezone = get_option( 'timezone_string' );
	$file = fopen( 'php://output', 'w' );

	foreach( $logs as $log ) {

		fputcsv( $file, array(
			$log['message_id'],							
			$log['status'],					
			$log['recipient'],							
			$log['email'],
			$log['subject'],
			apply_filters( 'email_message_csv', $log['message'] ),
			implode( '; ', maybe_unserialize( $log['headers'] ) ),
			implode( '; ', maybe_unserialize( $log['attachments'] ) ),
			kts_ts2time( $log['sent'], $timezone )
		) );

	}

	fclose( $file );
	exit;
}


/* PARSE HTML EMAIL MESSAGE */
function kts_parse_html_message( $message ) {
	$html = strip_tags( wp_specialchars_decode( $message, ENT_QUOTES ) );
	$stripped = preg_replace('~\.\s~', '', $html, 1 );
	return str_replace( 'TryFile', '', $stripped );
}
add_filter( 'email_message', 'kts_parse_html_message' );

function kts_parse_html_message_in_csv_export( $message ) {
	preg_match_all( '~<p>(.*?)<\/p>~s', wp_specialchars_decode( $message, ENT_QUOTES ), $match );
	return implode( ' ', $match[1] );
}
add_filter( 'email_message_csv', 'kts_parse_html_message_in_csv_export' );


# DELETE OLD LOGS
function kts_delete_old_email_logs() {

	global $wpdb;		
	$table_name = $wpdb->prefix . 'kts_email_logs';
	$email_logs = get_option( 'email-logs' );
	
	# Set length of time before deletion (with default of one week)
	$storage = WEEK_IN_SECONDS;
	$storage_options = array( 0, 604800, 1209600, 1814400, 2419200, 15780000 );

	if ( isset( $email_logs['storage'] ) && in_array( $email_logs['storage'], $storage_options ) ) {
		if ( $email_logs['storage'] === 0 ) { // do not delete logs
			return;
		}
		$storage = $email_logs['storage'];
	}
	$time_ago = time() - $storage;

	# $wpdb->query does not sanitize data, so use $wpdb->prepare
	$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE sent < %d", $time_ago ) );
}
add_action( 'kts_email_logs_hook', 'kts_delete_old_email_logs' );

function kts_email_logs_cronjobs() {
	if ( ! wp_next_scheduled( 'kts_email_logs_hook' ) ) {
		wp_schedule_event( time(), 'hourly', 'kts_email_logs_hook' );
	}
}	
register_activation_hook( __FILE__, 'kts_email_logs_cronjobs' );
