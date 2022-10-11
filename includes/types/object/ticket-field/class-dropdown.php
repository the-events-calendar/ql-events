<?php
/**
 * Ticket Field type Dropdown.
 *
 * Registers "TicketFieldDropdown" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Dropdown
 */
class Dropdown {
	/**
	 * Registers type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldDropdown',
			[
				'interfaces'  => [ 'TicketField' ],
				'description' => __( 'Dropdown ticket field', 'ql-events' ),
				'fields'      => [
					'options' => [
						'type'        => [ 'list_of' => 'String' ],
						'description' => __( 'Is this field required?', 'ql-events' ),
						'resolve'     => function( $field ) {
							return $field->options;
						},
					],
				],
			]
		);
	}
}
