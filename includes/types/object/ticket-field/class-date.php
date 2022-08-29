<?php
/**
 * Ticket Field type Date.
 *
 * Registers "TicketFieldDate" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Date
 */
class Date {
	/**
	 * Registers type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldDate',
			[
				'interfaces'  => [ 'TicketField' ],
				'description' => __( 'Date ticket field', 'ql-events' ),
				'fields'      => [],
			]
		);
	}
}
