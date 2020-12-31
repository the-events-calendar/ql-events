<?php
/**
 * Connection - RSVPAttendees
 *
 * Registers connections to RSVPAttendee
 *
 * @package WPGraphQL\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Connection;

use Tribe__Tickets__RSVP as RSVP;
use WPGraphQL\Connection\PostObjects;

/**
 * Class - RSVPAttendees
 */
class RSVPAttendees extends PostObjects {
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
					'toType'        => 'RSVPAttendee',
					'fromFieldName' => 'rSVPAttendees',
				)
			)
		);
	}

	public static function where_args() {
		return array(
			'eventsIn' => array(
				'type' => array( 'list_of' => 'ID'),
				'description' => __( 'Filter the connection based on event Id'),
			)
		);
	}
}
