<?php
/**
 * Plugin Name: QL Events
 * Description: Adds The Events Calendar Functionality to WPGraphQL schema.
 * Version: 0.3.0
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
		define( 'QL_EVENTS_VERSION', '0.3.0' );
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
 * Returns path to plugin root directory.
 *
 * @return string
 */
function get_plugin_directory() {
	return trailingslashit( QL_EVENTS_PLUGIN_DIR );
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
 * Renders admin notice for error.
 *
 * @param array $msg  Error message.
 *
 * @return void
 */
function add_admin_notice( $msg ) {
	add_action(
		'admin_notices',
		function() use ( $msg ) {
			?>
			<div class="notice notice-error">
				<p>
					<?php echo esc_html( $msg ); ?>
				</p>
			</div>
			<?php
		}
	);
}

/**
 * Renders admin notice for missing dependencies.
 *
 * @param array $dep  Missing Dependencies.
 *
 * @return void
 */
function add_missing_dependencies_notice( $dep ) {
	add_admin_notice(
		printf(
			/* translators: dependency not ready error message */
			'<a href="%1$s" target="_blank">%2$s</a> must be active for "QL Events" to work',
			esc_attr( $dep[0] ),
			esc_html( $dep[1] )
		)
	);
}

// Load constants.
constants();

/**
 * Initializes WooGraphQL Pro
 */
require_once get_includes_directory() . 'class-ql-events.php';
QL_Events::instance();

// Load access functions.
require_once get_plugin_directory() . 'access-functions.php';
