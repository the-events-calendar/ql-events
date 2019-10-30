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

/**
 * Class - Tickets
 */
class Tickets extends PostObjects {
	/**
	 * Registers the various connections from other Types to Tickets
	 */
	public static function register_connections() {
		if ( \QL_Events::is_ticket_events_loaded() ) {
			// From Event to RSVPTickets.
			$rsvp = tribe( 'tickets.rsvp' );
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
			$paypal = tribe( 'tickets.commerce.paypal' );
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
