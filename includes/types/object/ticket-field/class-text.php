<?php
/**
 * Ticket Field type Text.
 *
 * Registers "TicketFieldText" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Text
 */
class Text {
	/**
	 * Registers type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldText',
			array(
				'interfaces'  => array( 'TicketField' ),
				'description' => __( 'Text ticket field', 'ql-events' ),
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
