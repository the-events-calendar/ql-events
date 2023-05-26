<?php
/**
 * Connection - TicketsPlus
 *
 * Registers Tickets Plus connections and hooks.
 *
 * @package WPGraphQL\QL_Events\Connection
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Connection;

use WPGraphQL\WooCommerce\Connection\Products;

/**
 * Class - Tickets_Plus
 */
class Tickets_Plus extends Tickets {


	/**
	 * Registers ET Plus types as available ticket type on existing ET connections.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register_available_plus_ticket_types() {
		add_filter(
			'ql_events_ticket_connection_ticket_classes',
			function ( $ticket_classes, $source, $args, $context, $info ) {
				if ( 'tickets' === $info->fieldName ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$ticket_classes[] = 'tickets-plus.commerce.woo';
				}

				return $ticket_classes;
			},
			10,
			5
		);
	}

	/**
	 * Registers the various connections from other Types to Product
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register_connections() {
		// From Event to WooTicket.
		register_graphql_connection(
			[
				'fromType'       => 'Event',
				'toType'         => 'SimpleProduct',
				'fromFieldName'  => 'wooTickets',
				'connectionArgs' => Products::get_connection_args(),
				'resolve'        => self::get_event_to_ticket_resolver( [ 'tickets-plus.commerce.woo' ] ),
			]
		);
	}
}
