<?php
/**
 * WPObject Type - PayPalTicket
 *
 * Registers "PayPalTicket" WPObject type fields
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
class PayPalTicket_Type {
	/**
	 * Registers "Attendee" type fields.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register_fields() {
		register_graphql_fields(
			'PayPalTicket',
			[]
		);
	}
}
