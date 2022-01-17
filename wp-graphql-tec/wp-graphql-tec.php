<?php
/**
 * Plugin Name: WPGraphQL for The Events Calendar
 * Plugin URI: https://www.wpgraphql.com
 * Description: Adds support for The Events Calendar suite of plugins to WPGraphQL
 * Author: Dovid Levine
 * Author URI: https://www.wpgraphql.com
 * Text Domain: wp-graphql-tec
 * Domain Path: /languages
 * Version: 0.0.1
 * Requires at least: 5.4.1
 * Tested up to: 5.7.2
 * Requires PHP: 7.4
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * WPGraphQL requires at least: 1.3.9+
 *
 * @package     WPGraphQL\WooCommerce
 * @author      justlevine
 * @license     GPL-3
 */

/**
 * Define plugin constants.
 */
function tec_graphql_constants() : void {
		// Plugin version.
	if ( ! defined( 'WPGRAPHQL_TEC_VERSION' ) ) {
		define( 'WPGRAPHQL_TEC_VERSION', '0.0.1' );
	}

			// Plugin Folder Path.
	if ( ! defined( 'WPGRAPHQL_TEC_PLUGIN_DIR' ) ) {
		define( 'WPGRAPHQL_TEC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}

			// Plugin Folder URL.
	if ( ! defined( 'WPGRAPHQL_TEC_PLUGIN_URL' ) ) {
		define( 'WPGRAPHQL_TEC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

			// Plugin Root File.
	if ( ! defined( 'WPGRAPHQL_TEC_PLUGIN_FILE' ) ) {
		define( 'WPGRAPHQL_TEC_PLUGIN_FILE', __FILE__ );
	}

			// Whether to autoload the files or not.
	if ( ! defined( 'WPGRAPHQL_TEC_AUTOLOAD' ) ) {
		define( 'WPGRAPHQL_TEC_AUTOLOAD', true );
	}
}

/**
 * Checks if all the the required plugins are installed and activated.
 */
function tec_graphql_dependencies_not_ready() : array {
	$deps = [];
	if ( ! class_exists( '\WPGraphQL' ) ) {
		$deps[] = 'WPGraphQL';
	}

	if( ! class_exists( 'Tribe__Events__Main') && ! class_exists( 'Tribe__Tickets__Main') ){
		$deps[] = 'The Events Calendar or Event Tickets';
	}

	return $deps;
}

/**
 * Initializes WPGraphQL TEC
 * 
 * @return \WPGraphQL\TEC\TEC|false
 */
function tec_graphql_init() {
	tec_graphql_constants();

	$not_ready = tec_graphql_dependencies_not_ready();

	if ( empty( $not_ready ) && defined('WPGRAPHQL_TEC_PLUGIN_DIR')) {
		require_once WPGRAPHQL_TEC_PLUGIN_DIR . 'src/TEC.php';
		return \WPGraphQL\TEC\TEC::instance();
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
								esc_html__( '%1$s must be active for WPGraphQL for The Events Calendar to work', 'wp-graphql-tec' ),
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
add_action( 'graphql_init', 'tec_graphql_init' );
