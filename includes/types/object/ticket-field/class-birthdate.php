<?php
/**
 * Ticket Field type Birthdate.
 *
 * Registers "TicketFieldBirthdate" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Birthdate
 */
class Birthdate {
	/**
	 * Registers type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldBirthdate',
			array(
				'interfaces'  => array( 'TicketField' ),
				'description' => __( 'Birthdate ticket field', 'ql-events' ),
				'fields'      => array(
				)
			)
		);
	}
}
