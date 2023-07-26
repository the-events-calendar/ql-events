<?php
/**
 * Adds filters that modify core schema.
 *
 * @package \WPGraphQL\QL_Events
 * @since   0.3.0
 */

namespace WPGraphQL\QL_Events;

use Tribe__Events__Main as Main;

/**
 * Class Tickets_Plus_Filters
 */
class Tickets_Plus_Filters {

	/**
	 * Register filters
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public static function add_filters() {
		add_filter( 'register_post_type_args', [ __CLASS__, 'register_post_types' ], 10, 2 );
		add_filter(
			'graphql_wp_object_type_config',
			[
				self::class,
				'assign_ticket_interface',
			],
			10,
			2
		);

		add_filter(
			'graphql_product_connection_query_args',
			[
				'\WPGraphQL\QL_Events\Data\Connection\Ticket_Connection_Resolver',
				'get_ticket_plus_args',
			],
			10,
			5
		);

		add_filter(
			'graphql_product_connection_catalog_visibility',
			[
				'\WPGraphQL\QL_Events\Data\Connection\Ticket_Connection_Resolver',
				'get_ticket_plus_default_visibility',
			],
			10,
			6
		);
	}

	/**
	 * Register ET plus post types to GraphQL schema
	 *
	 * @since 0.3.0
	 *
	 * @param array  $args      - post-type args.
	 * @param string $post_type - post-type slug.
	 *
	 * @return array
	 */
	public static function register_post_types( $args, $post_type ) {
		if ( 'tribe_wooticket' === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'WooAttendee';
			$args['graphql_plural_name'] = 'WooAttendees';
		}

		return $args;
	}

	/**
	 * Filter callback for inject WPObject types with the "Ticket" interface.
	 *
	 * @since 0.3.0
	 *
	 * @param array $config  WPObject type config.
	 *
	 * @return array
	 */
	public static function assign_ticket_interface( $config ) {
		switch ( $config['name'] ) {
			case 'SimpleProduct':
				$config['interfaces'][] = 'Ticket';
				break;

			case 'Order':
				$config['interfaces'][] = 'TECOrder';
				break;

			case 'WooAttendee':
				$config['interfaces'][] = 'Attendee';
		}

		return $config;
	}
}
