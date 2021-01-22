<?php
/**
 * Plugin Name: QL Events
 * Description: Adds The Events Calendar Functionality to WPGraphQL schema.
 * Version: 0.0.1
 * Author: kidunot89
 * Author URI: https://axistaylor.com
 * Text Domain: ql-events
 * Domain Path: /languages
 *
 * @package     WPGraphQL\QL_Events
 * @author      kidunot89
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * If the codeception remote coverage file exists, require it.
 *
 * This file should only exist locally or when CI bootstraps the environment for testing
 */
if ( file_exists( __DIR__ . '/c3.php' ) ) {
	// Get tests output directory.
	$test_dir = __DIR__ . '/tests/output';
	define( 'C3_CODECOVERAGE_ERROR_LOG_FILE', $test_dir . '/c3_error.log' );
	require_once __DIR__ . '/c3.php';
}

/**
 * Setups QL Events constants
 */
function ql_events_constants() {
	// Plugin version.
	if ( ! defined( 'QL_EVENTS_VERSION' ) ) {
		define( 'QL_EVENTS_VERSION', '0.0.1' );
	}
	// Plugin Folder Path.
	if ( ! defined( 'QL_EVENTS_PLUGIN_DIR' ) ) {
		define( 'QL_EVENTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}
	// Plugin Folder URL.
	if ( ! defined( 'QL_EVENTS_PLUGIN_URL' ) ) {
		define( 'QL_EVENTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}
	// Plugin Root File.
	if ( ! defined( 'QL_EVENTS_PLUGIN_FILE' ) ) {
		define( 'QL_EVENTS_PLUGIN_FILE', __FILE__ );
	}
	// Whether to autoload the files or not.
	if ( ! defined( 'QL_EVENTS_AUTOLOAD' ) ) {
		define( 'QL_EVENTS_AUTOLOAD', true );
	}
}

/**
 * Checks if QL Events required plugins are installed and activated
 */
function ql_events_dependencies_not_ready() {
	$deps = [];
	if ( ! class_exists( 'WPGraphQL' ) ) {
		$deps[] = 'WPGraphQL';
	}
	if ( ! class_exists( 'Tribe__Events__Main' ) ) {
		$deps[] = 'The Events Calendar';
	}

	return $deps;
}

/**
 * Initializes QL Events
 */
function ql_events_init() {
	ql_events_constants();

	$not_ready = ql_events_dependencies_not_ready();
	if ( empty( $not_ready ) ) {
		require_once QL_EVENTS_PLUGIN_DIR . 'includes/class-ql-events.php';
		return QL_Events::instance();
	}

	foreach ( $not_ready as $dep ) {
		add_action(
			'admin_notices',
			function() use ( $dep ) {
				?>
				<div class="error notice">
					<p>
						<?php
							printf(
								/* translators: dependency not ready error message */
								esc_html__( '%1$s must be active for QL Events to work', 'ql-events' ),
								esc_html( $dep )
							);
						?>
					</p>
				</div>
				<?php
			}
		);
	}

	return false;
}
add_action( 'graphql_init', 'ql_events_init' );
