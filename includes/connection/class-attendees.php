<?php
/**
 * Connection - Attendees
 *
 * Registers connections to Attendee
 *
 * @package WPGraphQL\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Connection;

use Tribe__Tickets__RSVP as RSVP;
use WPGraphQL\Connection\PostObjects;

/**
 * Class - Attendees
 */
class Attendees extends PostObjects {
	/**
	 * Registers the various connections from other Types to Attendees
	 */
	public static function register_connections() {
		// From Event to Attendees.
		register_graphql_connection(
			self::get_connection_config(
				get_post_type_object( RSVP::ATTENDEE_OBJECT ),
				array(
					'fromType'      => 'Event',
					'toType'        => 'Attendee',
					'fromFieldName' => 'attendees',
				)
			)
		);
	}
}
