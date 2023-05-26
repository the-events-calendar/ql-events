<?php
/**
 * Ticket Field type E-mail.
 *
 * Registers "TicketFieldEmail" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Email
 */
class Email {
	/**
	 * Registers type.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldEmail',
			[
				'interfaces'  => [ 'TicketField' ],
				'description' => __( 'E-mail ticket field', 'ql-events' ),
				'fields'      => [
					'placeholder' => [
						'type'        => 'String',
						'description' => __( 'Field input placeholder', 'ql-events' ),
						'resolve'     => function( $field ) {
							return ! empty( $field->placeholder ) ? $field->placeholder : null;
						},
					],
				],
			]
		);

		register_graphql_field(
			'Product',
			'ticketFieldEmail',
			[
				'type'        => [ 'list_of' => 'TicketFieldEmail' ],
				'description' => __( 'Custom ticket fields on this ticket', 'ql-events' ),
				'resolve'     => function( $source ) {
					$ticket_id = $source->ID;

					$meta = tribe( 'tickets-plus.meta' );
					if ( $meta->ticket_has_meta( $ticket_id ) ) {
						$fields = $meta->get_meta_fields_by_ticket( $ticket_id );
						return array_filter(
							$fields,
							function( $field ) {
								return 'email' === $field->type;
							}
						);
					}

					return [];
				},
			]
		);

		register_graphql_field(
			'Event',
			'ticketFieldEmail',
			[
				'type'        => [ 'list_of' => 'TicketFieldEmail' ],
				'description' => __( 'All custom ticket fields on the tickets for this event', 'ql-events' ),
				'resolve'     => function( $source ) {
					$event_id = $source->ID;

					$fields = tribe( 'tickets-plus.meta' )->get_meta_fields_by_event( $event_id );
					return array_filter(
						$fields,
						function( $field ) {
							return 'email' === $field->type;
						}
					);
				},
			]
		);
	}
}
