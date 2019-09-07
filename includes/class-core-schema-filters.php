<?php
/**
 * Adds filters that modify core schema.
 *
 * @package \WPGraphQL\Extensions\QL_Events
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events;

use Tribe__Events__Main as Main;
/**
 * Class Core_Schema_Filters
 */
class Core_Schema_Filters {

	/**
	 * Register filters
	 */
	public static function add_filters() {
		add_action( 'register_post_type_args', array( __CLASS__, 'register_post_types' ), 10, 2 );
		add_action( 'register_taxonomy_args', array( __CLASS__, 'register_taxonomies' ), 10, 2 );

		add_filter(
			'graphql_post_object_connection_query_args',
			array( __CLASS__, 'organizer_connection_query_args' ),
			10,
			5
		);

		if ( \QL_Events::is_ticket_events_loaded() ) {
			add_filter(
				'graphql_post_object_connection_query_args',
				array( __CLASS__, 'ticket_connection_query_args' ),
				10,
				5
			);
		}

		if ( \QL_Events::is_ticket_events_plus_loaded() ) {
			add_filter(
				'graphql_post_object_connection_query_args',
				array( __CLASS__, 'ticket_plus_connection_query_args' ),
				10,
				5
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
		if ( MAIN::POSTTYPE === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Event';
			$args['graphql_plural_name'] = 'Events';
		}

		if ( MAIN::ORGANIZER_POST_TYPE === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Organizer';
			$args['graphql_plural_name'] = 'Organizers';
		}

		if ( MAIN::VENUE_POST_TYPE === $post_type ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'Venue';
			$args['graphql_plural_name'] = 'Venues';
		}

		if ( \QL_Events::is_ticket_events_loaded() ) {
			$ticket_types = array(
				'RSVP'   => tribe( 'tickets.rsvp' ),
				'PayPal' => tribe( 'tickets.commerce.paypal' ),
			);

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

		if ( \QL_Events::is_ticket_events_plus_loaded() ) {
			$wootickets = tribe( 'tickets-plus.commerce.woo' );
			if ( $instance::ATTENDEE_OBJECT === $post_type ) {
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
		if ( MAIN::TAXONOMY === $taxonomy ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'EventsCategory';
			$args['graphql_plural_name'] = 'EventsCategories';
		}

		return $args;
	}

	/**
	 * Filter PostObjectConnectionResolver's query_args and adds args to used when querying
	 * TEC's "Organizer" CPT
	 *
	 * @param array       $query_args - WP_Query args.
	 * @param mixed       $source     - Connection parent resolver.
	 * @param array       $args       - Connection arguments.
	 * @param AppContext  $context    - AppContext object.
	 * @param ResolveInfo $info       - ResolveInfo object.
	 *
	 * @return mixed
	 */
	public static function organizer_connection_query_args( $query_args, $source, $args, $context, $info ) {
		return \WPGraphQL\Extensions\QL_Events\Data\Connection\Organizer_Connection_Resolver::get_query_args( $query_args, $source, $args, $context, $info );
	}

	/**
	 * Filter PostObjectConnectionResolver's query_args and adds args to used when querying
	 * Ticket Events' "Ticket" CPTs
	 *
	 * @param array       $query_args - WP_Query args.
	 * @param mixed       $source     - Connection parent resolver.
	 * @param array       $args       - Connection arguments.
	 * @param AppContext  $context    - AppContext object.
	 * @param ResolveInfo $info       - ResolveInfo object.
	 *
	 * @return mixed
	 */
	public static function ticket_connection_query_args( $query_args, $source, $args, $context, $info ) {
		return \WPGraphQL\Extensions\QL_Events\Data\Connection\Ticket_Connection_Resolver::get_ticket_args( $query_args, $source, $args, $context, $info );
	}

	/**
	 * Filter PostObjectConnectionResolver's query_args and adds args to used when querying
	 * Ticket Events Plus' "Ticket" CPTs
	 *
	 * @param array       $query_args - WP_Query args.
	 * @param mixed       $source     - Connection parent resolver.
	 * @param array       $args       - Connection arguments.
	 * @param AppContext  $context    - AppContext object.
	 * @param ResolveInfo $info       - ResolveInfo object.
	 *
	 * @return mixed
	 */
	public static function ticket_plus_connection_query_args( $query_args, $source, $args, $context, $info ) {
		return \WPGraphQL\Extensions\QL_Events\Data\Connection\Ticket_Connection_Resolver::get_ticket_plus_args( $query_args, $source, $args, $context, $info );
	}
}
