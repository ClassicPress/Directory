<?php
/**
 * Validates and loads core connectors, integrated connectors, and
 * connectors registered using the "wp_stream_connectors" hook.
 *
 * @package WP_Stream
 */

namespace WP_Stream;

/**
 * Class - Connectors
 */
class Connectors {
	/**
	 * Holds instance of plugin object
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Registered connectors.
	 *
	 * @var array
	 */
	public $connectors = array();

	/**
	 * Contexts registered to Connectors
	 *
	 * @var array
	 */
	public $contexts = array();

	/**
	 * Action taxonomy terms
	 *
	 * Holds slug to localized label association
	 *
	 * @var array
	 */
	public $term_labels = array(
		'stream_connector' => array(),
		'stream_context'   => array(),
		'stream_action'    => array(),
	);

	/**
	 * Admin notice messages
	 *
	 * @var array
	 */
	protected $admin_notices = array();

	/**
	 * Class constructor.
	 *
	 * @param Plugin $plugin Instance of plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->load_connectors();
	}

	/**
	 * Load built-in connectors
	 */
	public function load_connectors() {
		$connectors = array(
			/**
			 * Core Connectors
			 */
			'blogs',
			'comments',
			'editor',
			'installer',
			'media',
			'menus',
			'posts',
			'settings',
			'taxonomies',
			'users',
			'widgets',

			/**
			 * Integrated Connectors
			 */
			'acf',
			'bbpress',
			'buddypress',
			'edd',
			'gravityforms',
			'jetpack',
			'mercator',
			'user-switching',
			'woocommerce',
			'wordpress-seo',
		);

		$classes = array();
		foreach ( $connectors as $connector ) {
			// Load connector class file.
			include_once $this->plugin->locations['dir'] . '/connectors/class-connector-' . $connector . '.php';

			// Set fully qualified class name.
			$class_name = sprintf( '\WP_Stream\Connector_%s', str_replace( '-', '_', $connector ) );

			// Bail if no class loaded.
			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			// Initialize connector.
			$class = new $class_name( $this->plugin->log );

			// Check if the connector class extends WP_Stream\Connector.
			if ( ! is_subclass_of( $class, 'WP_Stream\Connector' ) ) {
				continue;
			}

			// Check if the connector events are allowed to be registered in the WP Admin.
			if ( is_admin() && ! $class->register_admin ) {
				continue;
			}

			// Check if the connector events are allowed to be registered in the WP Frontend.
			if ( ! is_admin() && ! $class->register_frontend ) {
				continue;
			}

			// Run any final validations the connector may have before used.
			if ( $class->is_dependency_satisfied() ) {
				$classes[ $class->name ] = $class;
			}
		}

		/**
		 * Allows for adding additional connectors via classes that extend Connector.
		 *
		 * @param array $classes An array of Connector objects.
		 */
		$this->connectors = apply_filters( 'wp_stream_connectors', $classes );

		if ( empty( $this->connectors ) ) {
			return;
		}

		foreach ( $this->connectors as $connector ) {
			if ( ! method_exists( $connector, 'get_label' ) ) {
				continue;
			}
			$this->term_labels['stream_connector'][ $connector->name ] = $connector->get_label();
		}

		// Get excluded connectors.
		$excluded_connectors = array();

		foreach ( $this->connectors as $connector ) {

			// Register error for invalid any connector class.
			if ( ! method_exists( $connector, 'get_label' ) ) {
				/* translators: %s: connector class name, intended to provide help to developers (e.g. "Connector_BuddyPress") */
				$this->plugin->admin->notice( sprintf( __( '%s class wasn\'t loaded because it doesn\'t implement the get_label method.', 'stream' ), $connector->name, 'Connector' ), true );
				continue;
			}
			if ( ! method_exists( $connector, 'register' ) ) {
				/* translators: %s: connector class name, intended to provide help to developers (e.g. "Connector_BuddyPress") */
				$this->plugin->admin->notice( sprintf( __( '%s class wasn\'t loaded because it doesn\'t implement the register method.', 'stream' ), $connector->name, 'Connector' ), true );
				continue;
			}
			if ( ! method_exists( $connector, 'get_context_labels' ) ) {
				/* translators: %s: connector class name, intended to provide help to developers (e.g. "Connector_BuddyPress") */
				$this->plugin->admin->notice( sprintf( __( '%s class wasn\'t loaded because it doesn\'t implement the get_context_labels method.', 'stream' ), $connector->name, 'Connector' ), true );
				continue;
			}
			if ( ! method_exists( $connector, 'get_action_labels' ) ) {
				/* translators: %s: connector class name, intended to provide help to developers (e.g. "Connector_BuddyPress") */
				$this->plugin->admin->notice( sprintf( __( '%s class wasn\'t loaded because it doesn\'t implement the get_action_labels method.', 'stream' ), $connector->name, 'Connector' ), true );
				continue;
			}

			// Check if the connectors extends the Connector class, if not skip it.
			if ( ! is_subclass_of( $connector, '\WP_Stream\Connector' ) ) {
				/* translators: %s: connector class name, intended to provide help to developers (e.g. "Connector_BuddyPress") */
				$this->plugin->admin->notice( sprintf( __( '%1$s class wasn\'t loaded because it doesn\'t extends the %2$s class.', 'stream' ), $connector->name, 'Connector' ), true );
				continue;
			}

			// Store connector label.
			if ( ! in_array( $connector->name, $this->term_labels['stream_connector'], true ) ) {
				$this->term_labels['stream_connector'][ $connector->name ] = $connector->get_label();
			}

			$connector_name = $connector->name;
			$is_excluded    = in_array( $connector_name, $excluded_connectors, true );

			/**
			 * Allows excluded connectors to be overridden and registered.
			 *
			 * @param bool   $is_excluded         True if excluded, otherwise false.
			 * @param string $connector           The current connector's slug.
			 * @param array  $excluded_connectors An array of all excluded connector slugs.
			 */
			$is_excluded_connector = apply_filters( 'wp_stream_check_connector_is_excluded', $is_excluded, $connector_name, $excluded_connectors );

			if ( $is_excluded_connector ) {
				continue;
			}

			$connector->register();

			// Link context labels to their connector.
			$this->contexts[ $connector->name ] = $connector->get_context_labels();

			// Add new terms to our label lookup array.
			$this->term_labels['stream_action']  = array_merge(
				$this->term_labels['stream_action'],
				$connector->get_action_labels()
			);
			$this->term_labels['stream_context'] = array_merge(
				$this->term_labels['stream_context'],
				$connector->get_context_labels()
			);
		}

		$labels = $this->term_labels['stream_connector'];

		/**
		 * Fires after all connectors have been registered.
		 *
		 * @param array      $labels     All register connectors labels array
		 * @param Connectors $connectors The Connectors object
		 */
		do_action( 'wp_stream_after_connectors_registration', $labels, $this );
	}

	/**
	 * Unregisters the context hooks for all connectors.
	 */
	public function unload_connectors() {
		foreach ( $this->connectors as $connector ) {
			$connector->unregister();
		}
	}

	/**
	 * Reregisters the context hooks for all connectors.
	 */
	public function reload_connectors() {
		foreach ( $this->connectors as $connector ) {
			$connector->register();
		}
	}

	/**
	 * Unregisters the context hooks for a connectors.
	 *
	 * @param string $name  Name of the connector.
	 */
	public function unload_connector( $name ) {
		if ( ! empty( $this->connectors[ $name ] ) ) {
			$this->connectors[ $name ]->unregister();
		}
	}

	/**
	 * Reregisters the context hooks for a connector.
	 *
	 * @param string $name  Name of the connector.
	 */
	public function reload_connector( $name ) {
		if ( ! empty( $this->connectors[ $name ] ) ) {
			$this->connectors[ $name ]->register();
		}
	}
}
