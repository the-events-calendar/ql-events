<?php
/**
 * Ticket Field type Date.
 *
 * Registers "TicketFieldDate" type.
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Type\WPObject\Ticket_Field;

/**
 * Class Date
 */
class Date {
	/**
	 * Registers type.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_object_type(
			'TicketFieldDate',
			[
				'interfaces'  => [ 'TicketField' ],
				'description' => __( 'Date ticket field', 'ql-events' ),
				'fields'      => [],
			]
		);

		register_graphql_field(
			'Product',
			'ticketFieldDate',
			[
				'type'        => [ 'list_of' => 'TicketFieldDate' ],
				'description' => __( 'Custom ticket fields on this ticket', 'ql-events' ),
				'resolve'     => function( $source ) {
					$ticket_id = $source->ID;

					$meta = tribe( 'tickets-plus.meta' );
					if ( $meta->ticket_has_meta( $ticket_id ) ) {
						$fields = $meta->get_meta_fields_by_ticket( $ticket_id );
						return array_filter(
							$fields,
							function( $field ) {
								return 'datetime' === $field->type;
							}
						);
					}

					return [];
				},
			]
		);

		register_graphql_field(
			'Event',
			'ticketFieldDate',
			[
				'type'        => [ 'list_of' => 'TicketFieldDate' ],
				'description' => __( 'All custom ticket fields on the tickets for this event', 'ql-events' ),
				'resolve'     => function( $source ) {
					$event_id = $source->ID;

					$fields = tribe( 'tickets-plus.meta' )->get_meta_fields_by_event( $event_id );
					return array_filter(
						$fields,
						function( $field ) {
							return 'datetime' === $field->type;
						}
					);
				},
			]
		);
	}
}
