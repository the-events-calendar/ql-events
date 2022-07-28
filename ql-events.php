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

namespace WPGraphQL\QL_Events;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setups QL Events constants
 */
function constants() {
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
 * Returns path to plugin "includes" directory.
 *
 * @return string
 */
function get_includes_directory() {
	return trailingslashit( QL_EVENTS_PLUGIN_DIR ) . 'includes/';
}

/**
 * Returns path to plugin "vendor" directory.
 *
 * @return string
 */
function get_vendor_directory() {
	return trailingslashit( QL_EVENTS_PLUGIN_DIR ) . 'vendor/';
}

/**
 * Checks if QL Events required plugins are installed and activated
 */
function dependencies_not_ready( &$deps = []) {
	if ( ! class_exists( 'WPGraphQL' ) ) {
		$deps[] = 'WPGraphQL';
	}
	if ( ! class_exists( 'Tribe__Events__Main' ) ) {
		$deps[] = 'The Events Calendar';
	}

	return empty( $deps );
}

/**
 * Initializes QL Events
 */
function init() {
	constants();
	if ( dependencies_not_ready( $not_ready ) ) {
		require get_includes_directory() . 'class-ql-events.php';
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
add_action( 'graphql_init', 'WPGraphQL\QL_Events\init' );
