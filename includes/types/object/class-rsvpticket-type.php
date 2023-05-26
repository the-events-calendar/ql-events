<?php
/**
 * WPObject Type - RSVPTicket
 *
 * Registers "RSVPTicket" WPObject type fields
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Class - Attendee_Type
 */
class RSVPTicket_Type {
	/**
	 * Registers "Attendee" type fields.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register_fields() {
		// Retrieve Ticket Events' RSVP class instance.
		$rsvp = tribe( 'tickets.rsvp' );
		register_graphql_fields(
			'RSVPTicket',
			[]
		);
	}
}
