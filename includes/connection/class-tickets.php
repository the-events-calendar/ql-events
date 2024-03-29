<?php
/**
 * Connection - Tickets
 *
 * Registers connections to Ticket
 *
 * @package WPGraphQL\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Connection;

use Tribe__Tickets__RSVP as RSVP;
use WPGraphQL\Type\Connection\PostObjects;
use WPGraphQL\WooCommerce\Connection\Products;
use WPGraphQL\WooCommerce\Data\Factory;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\QL_Events\QL_Events as QL_Events;

/**
 * Class - Tickets
 */
class Tickets extends PostObjects {

	/**
	 * Returns a connection resolver wrapped around the ticket repositories
	 * passed by classname.
	 *
	 * @since 0.3.1
	 *
	 * @param array $ticket_classes  Classnames of Ticket repository to be used.
	 *
	 * @return mixed
	 */
	protected static function get_event_to_ticket_resolver( $ticket_classes ) {
		return function( $source, $args, $context, $info ) use ( $ticket_classes ) {
			// Get ticket repository.
			$repository = tribe_tickets( 'default' );

			// Normalize ID.
			$post_id = $source->ID;
			if ( class_exists( '\TEC\Events\Custom_Tables\V1\Models\Occurrence', false ) ) {
				$post_id = \TEC\Events\Custom_Tables\V1\Models\Occurrence::normalize_id( $source->ID );
			}

			/**
			 * Filters ticket classes to add support for additional ticket types.
			 *
			 * @param array       $ticket_classes - TEC ticket class names.
			 * @param mixed       $source         - Connection parent resolver.
			 * @param array       $args           - Connection arguments.
			 * @param AppContext  $context        - AppContext object.
			 * @param ResolveInfo $info           - ResolveInfo object.
			 *
			 * @since 0.3.0
			 */
			$ticket_classes = apply_filters( 'ql_events_ticket_connection_ticket_classes', $ticket_classes, $source, $args, $context, $info );

			foreach ( $ticket_classes as $ticket_class ) {
				$ticket_post_types[ $ticket_class ] = tribe( $ticket_class )->ticket_object;
			}

			// Create connection resolver.
			$resolver = new PostObjectConnectionResolver(
				$source,
				$args,
				$context,
				$info,
				$ticket_post_types
			);

			// Set query args to ticket repository.
			$repository->by( 'event', $post_id );
			$repository->by( 'status', 'publish' );
			$repository->by( 'posts_per_page', -1 );
			$repository->by( 'post_type', $ticket_post_types );

			$ticket_ids = $repository->pluck( 'ID' );
			$resolver->set_query_arg( 'post__in', ! empty( $ticket_ids ) ? $ticket_ids : [ '0' ] );
			$resolver->set_query_arg( 'tribe_suppress_query_filters', true );

			// Resolve connection and return results.
			return $resolver->get_connection();
		};
	}
	/**
	 * Registers the various connections from other Types to Tickets
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register_connections() {
		$post_object_object     = get_post_type_object( tribe( 'tickets.rsvp' )->ticket_object );
		$available_ticket_types = [
			'tickets.rsvp',
			'tickets.commerce.paypal',
		];

		// From RootQuery to Tickets.
		register_graphql_connection(
			[
				'fromType'       => 'RootQuery',
				'toType'         => 'Ticket',
				'fromFieldName'  => 'tickets',
				'connectionArgs' => self::get_connection_args( [], $post_object_object ),
				'resolve'        => function( $source, $args, $context, $info ) {
					$ticket_types = array_values( tribe_tickets()->ticket_types() );
					$resolver     = new PostObjectConnectionResolver( $source, $args, $context, $info, $ticket_types );

					$connection = $resolver->get_connection();
					return $connection;
				},
			]
		);

		// From Event to Tickets.
		register_graphql_connection(
			[
				'fromType'       => 'Event',
				'toType'         => 'Ticket',
				'fromFieldName'  => 'tickets',
				'connectionArgs' => self::get_connection_args( [], $post_object_object ),
				'resolve'        => self::get_event_to_ticket_resolver( $available_ticket_types ),
			]
		);
		// From Event to RSVPTickets.
		register_graphql_connection(
			[
				'fromType'       => 'Event',
				'toType'         => 'RSVPTicket',
				'fromFieldName'  => 'rsvpTickets',
				'connectionArgs' => self::get_connection_args( [], $post_object_object ),
				'resolve'        => self::get_event_to_ticket_resolver( [ 'tickets.rsvp' ] ),
			]
		);

		// From Event to PayPalTickets.
		register_graphql_connection(
			[
				'fromType'       => 'Event',
				'toType'         => 'PayPalTicket',
				'fromFieldName'  => 'paypalTickets',
				'connectionArgs' => self::get_connection_args( [], $post_object_object ),
				'resolve'        => self::get_event_to_ticket_resolver( [ 'tickets.commerce.paypal' ] ),
			]
		);
	}
}
