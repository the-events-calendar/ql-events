<?php
/**
 * Ticket Field type Checkbox.
 *
 * Registers "TicketFieldCheckbox" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Checkbox
 */
class Checkbox {
	/**
	 * Registers type.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldCheckbox',
			[
				'interfaces'  => [ 'TicketField' ],
				'description' => __( 'Checkbox ticket field', 'ql-events' ),
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
			'ticketFieldCheckbox',
			[
				'type'        => [ 'list_of' => 'TicketFieldCheckbox' ],
				'description' => __( 'Custom ticket fields on this ticket', 'ql-events' ),
				'resolve'     => function( $source ) {
					$ticket_id = $source->ID;

					$meta = tribe( 'tickets-plus.meta' );
					if ( $meta->ticket_has_meta( $ticket_id ) ) {
						$fields = $meta->get_meta_fields_by_ticket( $ticket_id );
						return array_filter(
							$fields,
							function( $field ) {
								return 'checkbox' === $field->type;
							}
						);
					}

					return [];
				},
			]
		);

		register_graphql_field(
			'Event',
			'ticketFieldCheckbox',
			[
				'type'        => [ 'list_of' => 'TicketFieldCheckbox' ],
				'description' => __( 'All custom ticket fields on the tickets for this event', 'ql-events' ),
				'resolve'     => function( $source ) {
					$event_id = $source->ID;

					$fields = tribe( 'tickets-plus.meta' )->get_meta_fields_by_event( $event_id );
					return array_filter(
						$fields,
						function( $field ) {
							return 'checkbox' === $field->type;
						}
					);
				},
			]
		);
	}
}
