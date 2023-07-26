<?php
/**
 * Initializes a QL Events admin settings.
 *
 * @package WPGraphQL\QL_Events
 * @since 0.3.0
 */

namespace WPGraphQL\QL_Events;

use WPGraphQL\Admin\Settings\Settings;
use WPGraphQL\QL_Events\Admin\General;

/**
 * Class Admin
 */
class Admin {

	/**
	 * Admin constructor
	 */
	public function __construct() {
		$this->add_filters();
	}

	/**
	 * Registers filters.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public function add_filters() {
		add_action( 'graphql_register_settings', [ $this, 'register_settings' ] );
	}

	/**
	 * Registers the WooGraphQL Settings tab.
	 *
	 * @since 0.3.0
	 *
	 * @param Settings $manager  Settings Manager.
	 *
	 * @return void
	 */
	public function register_settings( Settings $manager ) {
		$manager->settings_api->register_section(
			'ql_events_settings',
			[ 'title' => __( 'QL Events', 'ql-events' ) ]
		);

		$manager->settings_api->register_fields(
			'ql_events_settings',
			General::get_fields(),
		);
	}
}
