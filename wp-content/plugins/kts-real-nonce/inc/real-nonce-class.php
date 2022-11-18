<?php

Class KTS_Real_Nonce {

	const option_root = 'real-nonce';

	public static function create_nonce( $name ) {

		if ( is_array( $name ) ) {
			if ( isset( $name['name'] ) ) {
				$name = $name['name'];
			} else {
				$name = 'nonce';
			}
		}

		$id = self::generate_id();
		$name = substr( $name, 0, 17 ) . '_' . $id;

		$nonce = md5( wp_salt( 'nonce' ) . $name . microtime( true ) );
		self::store_nonce( $nonce, $name );
		return array( 'name' => $name, 'value' => $nonce );
	}


	public static function create_nonce_field( $name = 'nonce' ) {
		if ( is_array( $name ) ) {
			if ( isset( $name['name'] ) ) {
				$name = $name['name'];
			} else {
				$name = 'nonce';
			}
		}

		$name = sanitize_key( $name );
		$nonce = self::create_nonce( $name );
		$nonce_field = '<input type="hidden" name="' . $nonce['name'] . '" value="' . $nonce['value'] . '">';
		return $nonce_field;
	}


	public static function check_nonce( $name, $value ) {
		if ( empty( $name ) || empty( $value ) ) {
			return false;
		}

		$name = sanitize_key( $name );
		$value = sanitize_key( $value );
		$nonce = self::fetch_nonce( $name );

		if ( $nonce !== $value ) {
			return false;
		}

		return true;
	}


	public static function store_nonce( $nonce, $name ) {
		if ( empty( $name ) ) {
			return false;
		}

		add_option( self::option_root . '_' . $name, sanitize_key( $nonce ) );
		add_option( self::option_root . '_expires_' . $name, time() + 86400 );
		return true;
	}


	protected static function fetch_nonce( $name ) {
		$fetched_value = get_option( self::option_root . '_' . $name );
		$nonce_expires = get_option( self::option_root . '_expires_' . $name );
		
		self::delete_nonce( $name );
		
		if ( $nonce_expires < time() ) {
			$fetched_value = null;
		}

		return sanitize_key( $fetched_value );
	}


	public static function delete_nonce( $name ) {
		$option_deleted = delete_option( self::option_root . '_' . $name );
		$option_deleted = $option_deleted && delete_option( self::option_root . '_expires_' . $name );
		return (bool) $option_deleted;
	}


	public static function clear_nonces( $force = false ) {
		if ( defined( 'WP_SETUP_CONFIG' ) or defined( 'WP_INSTALLING' ) ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->options;
		$sql = $wpdb->prepare( "SELECT option_id, option_name, option_value FROM $table_name WHERE option_name like %s", self::option_root . '_expires_%' );
		$rows = $wpdb->get_results( $sql );

		$nonces_deleted = 0;

		foreach ( $rows as $single_nonce ) {

			if ( $force or ( $single_nonce->option_value < time() + 86400 ) ) {
				$name = substr( $single_nonce->option_name, strlen( self::option_root . '_expires_' ) );
				$nonces_deleted += ( self::delete_nonce( $name ) ? 1 : 0 );
			}
		}

		return (int) $nonces_deleted;

	}


	protected static function generate_id() {
		require_once( ABSPATH . 'wp-includes/class-phpass.php' );
		$hasher = new PasswordHash( 8, false );
		return md5( $hasher->get_random_bytes( 100, false ) );
	}

}
