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
			array(
				'interfaces'  => array( 'TicketField' ),
				'description' => __( 'Dropdown ticket field', 'ql-events' ),
				'fields'      => array(
					'options'      => array(
						'type'        => array( 'list_of' => 'String' ),
						'description' => __( 'Is this field required?', 'wp-graphql' ),
						'resolve'     => function( $field ) {
							return $field->options;
						},
					),
				)
			)
		);
	}
}
