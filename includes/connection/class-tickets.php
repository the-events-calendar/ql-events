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
use WPGraphQL\Connection\PostObjects;
use WPGraphQL\WooCommerce\Connection\Products;
use WPGraphQL\WooCommerce\Data\Factory;
use \WPGraphQL\Data\Connection\PostObjectConnectionResolver;

/**
 * Class - Tickets
 */
class Tickets extends PostObjects {
	/**
	 * Registers the various connections from other Types to Tickets
	 */
	public static function register_connections() {
		if ( \QL_Events::is_ticket_events_loaded() ) {
			$rsvp = tribe( 'tickets.rsvp' );
			$paypal = tribe( 'tickets.commerce.paypal' );

			// From RootQuery to Tickets.
			register_graphql_connection(
				array(
					'fromType'       => 'RootQuery',
					'toType'         => 'Ticket',
					'fromFieldName'  => 'tickets',
					'connectionArgs' => self::get_connection_args( [], get_post_type_object( $rsvp->ticket_object ) ),
					'resolve'        => function( $source, $args, $context, $info ) {
						$ticket_types = array_values( tribe_tickets()->ticket_types() );
						$resolver     = new PostObjectConnectionResolver( $source, $args, $context, $info, $ticket_types );

						$connection = $resolver->get_connection();
						return $connection;
					},
				)
			);

			// From Event to Tickets.
			register_graphql_connection(
				array(
					'fromType'       => 'Event',
					'toType'         => 'Ticket',
					'fromFieldName'  => 'tickets',
					'connectionArgs' => self::get_connection_args( [], get_post_type_object( $rsvp->ticket_object ) ),
					'resolve'        => function( $source, $args, $context, $info ) {
						$ticket_types = array_values( tribe_tickets()->ticket_types() );
						$resolver     = new PostObjectConnectionResolver( $source, $args, $context, $info, $ticket_types );

						$meta_query = array( 'relation' => 'OR' );
						foreach( tribe_tickets()->ticket_to_event_keys() as $meta_key ) {
							$meta_query[] = array(
								'key'     => $meta_key,
								'value'   => $source->ID,
								'compare' => '=',
								'type'    => 'NUMERIC',
							);
						}

						$resolver->set_query_arg( 'meta_query', $meta_query );

						$connection = $resolver->get_connection();
						return $connection;
					},
				)
			);
			// From Event to RSVPTickets.
			register_graphql_connection(
				self::get_connection_config(
					get_post_type_object( $rsvp->ticket_object ),
					array(
						'fromType'      => 'Event',
						'toType'        => 'RSVPTicket',
						'fromFieldName' => 'rsvpTickets',
					)
				)
			);
			// From Event to PayPalTickets.
			register_graphql_connection(
				self::get_connection_config(
					get_post_type_object( $paypal->ticket_object ),
					array(
						'fromType'      => 'Event',
						'toType'        => 'PayPalTicket',
						'fromFieldName' => 'paypalTickets',
					)
				)
			);
		}
	}
}
