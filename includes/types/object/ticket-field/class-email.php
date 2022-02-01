<?php
/**
 * Ticket Field type E-mail.
 *
 * Registers "TicketFieldEmail" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Email
 */
class Email {
	/**
	 * Registers type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldEmail',
			array(
				'interfaces'  => array( 'TicketField' ),
				'description' => __( 'E-mail ticket field', 'ql-events' ),
				'fields'      => array(
					'placeholder'      => array(
						'type'        => 'String',
						'description' => __( 'Field input placeholder', 'wp-graphql' ),
						'resolve'     => function( $field ) {
							return 'on' === $field->placeholder;
						},
					),
				)
			)
		);
	}
}
