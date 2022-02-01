<?php
/**
 * Ticket Field type Checkbox.
 *
 * Registers "TicketFieldCheckbox" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Checkbox
 */
class Checkbox {
	/**
	 * Registers type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldCheckbox',
			array(
				'interfaces'  => array( 'TicketField' ),
				'description' => __( 'Checkbox ticket field', 'ql-events' ),
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
