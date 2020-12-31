<?php
/**
 * Connection - PayPalAttendees
 *
 * Registers connections to PayPalAttendee
 *
 * @package WPGraphQL\QL_Events\Connection
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Connection;

use Tribe__Tickets__Commerce__PayPal__Main as PAYPAL;
use WPGraphQL\Connection\PostObjects;

/**
 * Class - Attendees
 */
class PayPalAttendees extends PostObjects {
	/**
	 * Registers the various connections from other Types to Attendees
	 */
	public static function register_connections() {
		// From Event to Attendees.
		register_graphql_connection(
			self::get_connection_config(
				get_post_type_object( PAYPAL::ATTENDEE_OBJECT ),
				array(
					'fromType'      => 'Event',
					'toType'        => 'PayPalAttendee',
					'fromFieldName' => 'payPalAttendees',
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
