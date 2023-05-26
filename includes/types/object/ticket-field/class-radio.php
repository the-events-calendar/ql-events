<?php
/**
 * Ticket Field type Radio button.
 *
 * Registers "TicketFieldRadio" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Radio
 */
class Radio {
	/**
	 * Registers type.
	 *
	 * @since 0.1.0
	 *
	 * @return void
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
							if ( ! empty( $field->extra ) ) {
								return ! empty( $field->extra['options'] ) ? $field->extra['options'] : [];
							}

							return null;
						},
					],
				],
			]
		);

		register_graphql_field(
			'Product',
			'ticketFieldRadio',
			[
				'type'        => [ 'list_of' => 'TicketFieldRadio' ],
				'description' => __( 'Custom ticket fields on this ticket', 'ql-events' ),
				'resolve'     => function( $source ) {
					$ticket_id = $source->ID;

					$meta = tribe( 'tickets-plus.meta' );
					if ( $meta->ticket_has_meta( $ticket_id ) ) {
						$fields = $meta->get_meta_fields_by_ticket( $ticket_id );
						return array_filter(
							$fields,
							function( $field ) {
								return 'radio' === $field->type;
							}
						);
					}

					return [];
				},
			]
		);

		register_graphql_field(
			'Event',
			'ticketFieldRadio',
			[
				'type'        => [ 'list_of' => 'TicketFieldRadio' ],
				'description' => __( 'All custom ticket fields on the tickets for this event', 'ql-events' ),
				'resolve'     => function( $source ) {
					$event_id = $source->ID;

					$fields = tribe( 'tickets-plus.meta' )->get_meta_fields_by_event( $event_id );
					return array_filter(
						$fields,
						function( $field ) {
							return 'radio' === $field->type;
						}
					);
				},
			]
		);
	}
}
