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
use WPGraphQL\Extensions\WooCommerce\Connection\Products;
use WPGraphQL\Extensions\WooCommerce\Data\Factory;

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
						'fromType'       => 'Event',
						'toType'         => 'Product',
						'fromFieldName'  => 'wooTickets',
						'connectionArgs' => Products::get_connection_args(),
						'resolveNode'    => function( $id, $args, $context, $info ) {
							return Factory::resolve_crud_object( $id, $context );
						},
						'resolve'        => function ( $source, $args, $context, $info ) {
							return Factory::resolve_product_connection( $source, $args, $context, $info );
						},
					)
				)
			);
		}
	}
}
