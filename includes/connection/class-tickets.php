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

		// From Event to Tickets.
		register_graphql_connection(
			self::get_connection_config(
				get_post_type_object( $rsvp->ticket_object ),
				array(
					'fromType'      => 'Event',
					'toType'        => 'Ticket',
					'fromFieldName' => 'tickets',
				)
			)
		);

		// From Attendee to Tickets.
		register_graphql_connection(
			self::get_connection_config(
				get_post_type_object( $rsvp->ticket_object ),
				array(
					'fromType'      => 'Attendee',
					'toType'        => 'Ticket',
					'fromFieldName' => 'tickets',
				)
			)
		);
	}
}
