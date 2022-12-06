<?php
/**
 * Adds filters that modify core schema.
 *
 * @package \WPGraphQL\QL_Events
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events;

use Tribe__Events__Main as Main;

/**
 * Class Core_Schema_Filters
 */
class Core_Schema_Filters {
	/**
	 * Simple "endsWith" function because PHP still doesn't have on built-in.
	 *
	 * @param string $haystack  Source string.
	 * @param string $needle    Target substring.
	 *
	 * @return bool
	 */
	private static function ends_with( $haystack, $needle ) {
		$length = strlen( $needle );
		if ( 0 === $length ) {
			return true;
		}

		return ( substr( $haystack, -$length ) === $needle );
	}

	/**
	 * Register filters
	 */
	public static function add_filters() {
		add_action( 'register_post_type_args', [ __CLASS__, 'register_post_types' ], 10, 2 );
		add_action( 'register_taxonomy_args', [ __CLASS__, 'register_taxonomies' ], 10, 2 );

		add_filter(
			'graphql_input_fields',
			[ __CLASS__, 'events_where_args' ],
			10,
			2
		);

		add_filter(
			'graphql_post_object_connection_query_args',
			[
				'\WPGraphQL\QL_Events\Data\Connection\Event_Connection_Resolver',
				'get_query_args',
			],
			10,
			5
		);

		add_filter(
			'graphql_post_object_connection_query_args',
			[
				'\WPGraphQL\QL_Events\Data\Connection\Organizer_Connection_Resolver',
				'get_query_args',
			],
			10,
			5
		);

		add_filter( 'graphql_data_is_private', [ __CLASS__, 'is_cpt_private' ], 10, 6 );

		if ( QL_Events::is_ticket_events_loaded() ) {
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

		if ( QL_Events::is_ticket_events_plus_loaded() ) {
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
	}

	/**
	 * Register TEC post types to GraphQL schema
	 *
	 * @param array  $args      - post-type args.
	 * @param string $post_type - post-type slug.
	 *
	 * @return array
	 */
	public static function register_post_types( $args, $post_type ) {
		if ( Main::POSTTYPE === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Event';
			$args['graphql_plural_name'] = 'Events';
		}

		if ( Main::ORGANIZER_POST_TYPE === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Organizer';
			$args['graphql_plural_name'] = 'Organizers';
		}

		if ( Main::VENUE_POST_TYPE === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Venue';
			$args['graphql_plural_name'] = 'Venues';
		}

		if ( QL_Events::is_ticket_events_loaded() ) {
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
		}

		if ( QL_Events::is_ticket_events_plus_loaded() ) {
			if ( 'tribe_wooticket' === $post_type ) {
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'WooAttendee';
				$args['graphql_plural_name'] = 'WooAttendees';
			}
		}

		return $args;
	}

	/**
	 * Register TEC taxonomies to GraphQL schema
	 *
	 * @param array  $args     - taxonomy args.
	 * @param string $taxonomy - taxonomy slug.
	 *
	 * @return array
	 */
	public static function register_taxonomies( $args, $taxonomy ) {
		if ( Main::TAXONOMY === $taxonomy ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'EventsCategory';
			$args['graphql_plural_name'] = 'EventsCategories';
		}

		return $args;
	}

	/**
	 * Adds "where" arguments to Event connections
	 *
	 * @param array  $fields     Event where args.
	 * @param string $type_name  Connection "where" input type name.
	 *
	 * @return array
	 */
	public static function events_where_args( $fields, $type_name ) {
		if ( self::ends_with( $type_name, 'ToEventConnectionWhereArgs' ) ) {
			$fields = array_merge(
				$fields,
				Connection\Events::where_args()
			);
		}
		return $fields;
	}

	/**
	 * Filter callback for inject WPObject types with the "Ticket" interface.
	 *
	 * @param array $config  WPObject type config.
	 *
	 * @return array
	 */
	public static function assign_ticket_interface( $config ) {
		switch ( $config['name'] ) {
			case 'RSVPTicket':
			case 'PayPalTicket':
			case 'SimpleProduct':
				$config['interfaces'][] = 'Ticket';
				break;

			case 'PayPalOrder':
			case 'Order':
				$config['interfaces'][] = 'TECOrder';
				break;

			case 'RSVPAttendee':
			case 'PayPalAttendee':
			case 'WooAttendee':
				$config['interfaces'][] = 'Attendee';
		}

		return $config;
	}

	/**
	 * Filter to determine if the data should be considered private or not
	 *
	 * @param boolean     $is_private   Whether the model is private.
	 * @param string      $model_name   Name of the model the filter is currently being executed in.
	 * @param mixed       $data         The un-modeled incoming data.
	 * @param string|null $visibility   The visibility that has currently been set for the data at this point.
	 * @param null|int    $owner        The user ID for the owner of this piece of data.
	 * @param WP_User     $current_user The current user for the session.
	 *
	 * @return bool
	 */
	public static function is_cpt_private( $is_private, $model_name, $data, $visibility, $owner, $current_user ) {
		$post_type      = get_post_type( $data->ID );
		$tec_post_types = [ Main::POSTTYPE, Main::ORGANIZER_POST_TYPE, Main::VENUE_POST_TYPE ];

		if ( in_array( $post_type, $tec_post_types, true ) ) {
			return false;
		}

		return $is_private;
	}
}
