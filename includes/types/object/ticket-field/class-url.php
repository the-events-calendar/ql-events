<?php
/**
 * Ticket Field type URL.
 *
 * Registers "TicketFieldURL" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class URL
 */
class URL {
	/**
	 * Registers type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldURL',
			[
				'interfaces'  => [ 'TicketField' ],
				'description' => __( 'URL ticket field', 'ql-events' ),
				'fields'      => [
					'placeholder' => [
						'type'        => 'String',
						'description' => __( 'Field input placeholder', 'ql-events' ),
						'resolve'     => function( $field ) {
							return 'on' === $field->placeholder;
						},
					],
				],
			]
		);
	}
}
