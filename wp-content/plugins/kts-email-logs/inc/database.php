<?php

# CREATE TABLE FOR EMAIL LOGS
function kts_email_logs_create_db() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'kts_email_logs';

	# Set database version as 1.0 in absence of value stored in options table
	$version = (int) get_option( 'kts_email_logs_version', '1.0' );

	# Create database table
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		message_id INT NOT NULL AUTO_INCREMENT,
		status BOOL DEFAULT 1 NOT NULL,
		recipient VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL,
		subject VARCHAR(255) NOT NULL,
		message TEXT NOT NULL,
		sent INT NOT NULL,
		error VARCHAR(255) DEFAULT NULL,
		exception TINYINT DEFAULT NULL,
		headers TEXT DEFAULT NULL,
		attachments VARCHAR(255) DEFAULT NULL,
		PRIMARY KEY (message_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
