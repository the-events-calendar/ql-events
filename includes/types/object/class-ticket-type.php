<?php
/**
 * WPObject Type - Ticket
 *
 * Registers "Ticket" WPObject type and queries
 *
 * @package \WPGraphQL\Extensions\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\Extensions\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Class - Ticket_Type
 */
class Ticket_Type {
	/**
	 * Registers "Ticket" type fields.
	 */
	public static function register_fields() {
		register_graphql_fields(
			'Ticket',
			array()
		);
	}
}
