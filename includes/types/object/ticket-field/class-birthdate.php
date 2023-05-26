<?php
/**
 * Ticket Field type Birthdate.
 *
 * Registers "TicketFieldBirthdate" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Birthdate
 */
class Birthdate {
	/**
	 * Registers type.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldBirthdate',
			[
				'interfaces'  => [ 'TicketField' ],
				'description' => __( 'Birthdate ticket field', 'ql-events' ),
				'fields'      => [],
			]
		);

		register_graphql_field(
			'Product',
			'ticketFieldBirthdate',
			[
				'type'        => [ 'list_of' => 'TicketFieldBirthdate' ],
				'description' => __( 'Custom ticket fields on this ticket', 'ql-events' ),
				'resolve'     => function( $source ) {
					$ticket_id = $source->ID;

					$meta = tribe( 'tickets-plus.meta' );
					if ( $meta->ticket_has_meta( $ticket_id ) ) {
						$fields = $meta->get_meta_fields_by_ticket( $ticket_id );
						return array_filter(
							$fields,
							function( $field ) {
								return 'birth' === $field->type;
							}
						);
					}

					return [];
				},
			]
		);

		register_graphql_field(
			'Event',
			'ticketFieldBirthdate',
			[
				'type'        => [ 'list_of' => 'TicketFieldBirthdate' ],
				'description' => __( 'All custom ticket fields on the tickets for this event', 'ql-events' ),
				'resolve'     => function( $source ) {
					$event_id = $source->ID;

					$fields = tribe( 'tickets-plus.meta' )->get_meta_fields_by_event( $event_id );
					return array_filter(
						$fields,
						function( $field ) {
							return 'birth' === $field->type;
						}
					);
				},
			]
		);
	}
}
