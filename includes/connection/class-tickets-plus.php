<?php
/**
 * Connection - WooTickets
 *
 * Registers connections to Ticket Plus types
 *
 * @package WPGraphQL\Extensions\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Connection;

use WPGraphQL\Extensions\WooCommerce\Connection\Products;
use WPGraphQL\Extensions\WooCommerce\Data\Factory;

/**
 * Class - Tickets
 */
class Tickets_Plus extends Products {
	/**
	 * Registers the various connections from other Types to Tickets
	 */
	public static function register_connections() {
		if ( \QL_Events::is_ticket_events_plus_loaded() ) {
			// From Event to WooTicket.
			register_graphql_connection(
				self::get_connection_config(
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
