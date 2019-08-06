<?php
/**
 * WPObject Type - Attendee
 *
 * Registers "Attendee" WPObject type and queries
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
class Attendee_Type {
	/**
	 * Registers "Attendee" type fields.
	 */
	public static function register_fields() {
		register_graphql_fields(
			'Attendee',
			array()
		);
	}
}
