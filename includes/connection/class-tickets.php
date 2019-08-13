<?php
/**
 * Connection - Tickets
 *
 * Registers connections to Ticket
 *
 * @package WPGraphQL\Extensions\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Connection;

use Tribe__Tickets__RSVP as RSVP;
use WPGraphQL\Connection\PostObjects;

/**
 * Class - Tickets
 */
class Tickets extends PostObjects {
	/**
	 * Registers the various connections from other Types to Tickets
	 */
	public static function register_connections() {
		$rsvp = RSVP::get_instance();

		if ( TEC_EVENT_TICKETS_LOADED ) {
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

		if ( TEC_EVENT_TICKETS_PLUS_LOADED ) {
			// From Event to WooTicket.
			$woocommerce = tribe( 'tickets-plus.commerce.woo' );
			register_graphql_connection(
				self::get_connection_config(
					get_post_type_object( $woocommerce->ticket_object ),
					array(
						'fromType'      => 'Event',
						'toType'        => 'Product',
						'fromFieldName' => 'wooTickets',
					)
				)
			);
		}
	}
}
