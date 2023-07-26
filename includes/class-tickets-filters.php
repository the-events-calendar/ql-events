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
 * Class Tickets_Filters
 */
class Tickets_Filters {

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
			'graphql_post_object_connection_query_args',
			[
				'\WPGraphQL\QL_Events\Data\Connection\Ticket_Connection_Resolver',
				'get_ticket_args',
			],
			10,
			5
		);
	}

	/**
	 * Register ET post types to GraphQL schema
	 *
	 * @since 0.3.0
	 *
	 * @param array  $args      - post-type args.
	 * @param string $post_type - post-type slug.
	 *
	 * @return array
	 */
	public static function register_post_types( $args, $post_type ) {
		$ticket_types = [
			'RSVP'   => tribe( 'tickets.rsvp' ),
			'PayPal' => tribe( 'tickets.commerce.paypal' ),
		];

		foreach ( $ticket_types as $key => $instance ) {
			if ( $instance::ATTENDEE_OBJECT === $post_type ) {
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = "{$key}Attendee";
				$args['graphql_plural_name'] = "{$key}Attendees";
			}

			if ( $instance->ticket_object === $post_type ) {
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = "{$key}Ticket";
				$args['graphql_plural_name'] = "{$key}Tickets";
			}

			if ( $instance::ORDER_OBJECT === $post_type
				&& $instance::ORDER_OBJECT !== $instance::ATTENDEE_OBJECT ) {
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = "{$key}Order";
				$args['graphql_plural_name'] = "{$key}Orders";
			}
		}

		return $args;
	}

	/**
	 * Filter callback for inject WPObject types with the "Ticket" interface.
	 *
	 * @since 0.1.0
	 *
	 * @param array $config  WPObject type config.
	 *
	 * @return array
	 */
	public static function assign_ticket_interface( $config ) {
		switch ( $config['name'] ) {
			case 'RSVPTicket':
			case 'PayPalTicket':
				$config['interfaces'][] = 'Ticket';
				break;

			case 'PayPalOrder':
				$config['interfaces'][] = 'TECOrder';
				break;

			case 'RSVPAttendee':
			case 'PayPalAttendee':
				$config['interfaces'][] = 'Attendee';
		}

		return $config;
	}
}
