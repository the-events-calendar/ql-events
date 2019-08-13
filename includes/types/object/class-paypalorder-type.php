<?php
/**
 * WPObject Type - PayPalOrder
 *
 * Registers "PayPalOrder" WPObject type fields
 *
 * @package \WPGraphQL\Extensions\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Class - Attendee_Type
 */
class PayPalOrder_Type {
	/**
	 * Registers "Attendee" type fields.
	 */
	public static function register_fields() {
		register_graphql_fields(
			'PayPalOrder',
			array()
		);
	}
}
