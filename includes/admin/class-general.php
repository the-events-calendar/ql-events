<?php
/**
 * Defines QL Events's general settings.
 *
 * @package WPGraphQL\QL_Events\Admin
 * @since 0.3.0
 */

namespace WPGraphQL\QL_Events\Admin;

use WPGraphQL\QL_Events\QL_Events;

/**
 * General class
 */
class General extends Section {

	/**
	 * Returns General settings fields.
	 *
	 * @since 0.3.0
	 *
	 * @return array
	 */
	public static function get_fields() {
		$test_mode_status = QL_Events::is_test_mode_active() ? 'force enabled' : 'force disabled';
		$settings_status  = sprintf(
			/* translators: Test mode status */
			__( 'This setting is <strong>%s</strong>. The <strong>QL_EVENTS_TEST_MODE</strong> flag with code', 'ql-events' ),
			$test_mode_status,
		);

		return [
			[
				'name'     => 'enable_events_pro_support',
				'label'    => __( 'Enable The Events Calendar Pro support ', 'ql-events' ),
				'desc'     => ! defined( 'QL_EVENTS_TEST_MODE' )
					? __( 'Include fields, types, queries, and mutations for The Events Calendar Pro (Requires The Events Calendar Pro be installed and activated.)', 'ql-events' )
					: $settings_status,
				'type'     => 'checkbox',
				'default'  => 'off',
				'value'    => defined( 'QL_EVENTS_TEST_MODE' ) ? 'on' : ql_events_setting( 'enable_events_pro_support', 'off' ),
				'disabled' => defined( 'QL_EVENTS_TEST_MODE' ) ? true : false,
			],
			[
				'name'     => 'enable_event_tickets_support',
				'label'    => __( 'Enable Event Tickets support', 'ql-events' ),
				'desc'     => ! defined( 'QL_EVENTS_TEST_MODE' )
					? __( 'Include fields, types, queries, and mutations for Event Tickets (Requires Event Tickets be installed and activated.)', 'ql-events' )
					: $settings_status,
				'type'     => 'checkbox',
				'default'  => 'off',
				'value'    => defined( 'QL_EVENTS_TEST_MODE' ) ? 'on' : ql_events_setting( 'enable_event_tickets_support', 'off' ),
				'disabled' => defined( 'QL_EVENTS_TEST_MODE' ) ? true : false,
			],
			[
				'name'     => 'enable_event_tickets_plus_support',
				'label'    => __( 'Enable Event Tickets Plus support', 'ql-events' ),
				'desc'     => ! defined( 'QL_EVENTS_TEST_MODE' )
					? __( 'Include fields, types, queries, and mutations for Event Tickets Plus (Requires Event Tickets Plus be installed and activated.)', 'ql-events' )
					: $settings_status,
				'type'     => 'checkbox',
				'default'  => 'off',
				'value'    => defined( 'QL_EVENTS_TEST_MODE' ) ? 'on' : ql_events_setting( 'enable_event_tickets_plus_support', 'off' ),
				'disabled' => defined( 'QL_EVENTS_TEST_MODE' ) ? true : false,
			],
			[
				'name'     => 'enable_events_virtual_support',
				'label'    => __( 'Enable Event Virtual support', 'ql-events' ),
				'desc'     => ! defined( 'QL_EVENTS_TEST_MODE' )
					? __( 'Include fields, types, queries, and mutations for Event Virtual (Requires Event Virtual be installed and activated.)', 'ql-events' )
					: $settings_status,
				'type'     => 'checkbox',
				'default'  => 'off',
				'value'    => defined( 'QL_EVENTS_TEST_MODE' ) ? 'on' : ql_events_setting( 'enable_events_virtual_support', 'off' ),
				'disabled' => defined( 'QL_EVENTS_TEST_MODE' ) ? true : false,
			],
		];
	}
}
