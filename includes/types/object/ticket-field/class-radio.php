<?php
/**
 * Ticket Field type Radio button.
 *
 * Registers "TicketFieldRadio" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Radio
 */
class Radio {
	/**
	 * Registers type.
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldRadio',
			[
				'interfaces'  => [ 'TicketField' ],
				'description' => __( 'Radio button ticket field', 'ql-events' ),
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
