<?php
/**
 * Ticket Field interface type.
 *
 * @package WPGraphQL\QL_Events\Type\WPInterface
 * @since   0.2.0
 */

namespace WPGraphQL\QL_Events\Type\WPInterface;

/**
 * Class Ticket_Field
 */
class Ticket_Field {

	/**
	 * Register the Node interface
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public static function register_type() {
		register_graphql_interface_type(
			'TicketField',
			[
				'description' => __( 'A ticket custom field', 'ql-events' ),
				'fields'      => [
					'type'        => [
						'type'        => 'String',
						'description' => __( 'Field type', 'ql-events' ),
						'resolve'     => function( $field ) {
							return $field->type;
						},
					],
					'label'       => [
						'type'        => 'String',
						'description' => __( 'Field Label', 'ql-events' ),
						'resolve'     => function( $field ) {
							return $field->label;
						},
					],
					'description' => [
						'type'        => 'String',
						'description' => __( 'Field Description', 'ql-events' ),
						'resolve'     => function( $field ) {
							return $field->description;
						},
					],
					'required'    => [
						'type'        => 'Boolean',
						'description' => __( 'Is this field required?', 'ql-events' ),
						'resolve'     => function( $field ) {
							return 'on' === $field->required;
						},
					],
				],
				'resolveType' => function ( $field ) {
					switch ( $field->type ) {
						case 'birth':
							return 'TicketFieldBirthdate';
						case 'checkbox':
							return 'TicketFieldCheckbox';
						case 'datetime':
							return 'TicketFieldDate';
						case 'select':
							return 'TicketFieldDropdown';
						case 'email':
							return 'TicketFieldEmail';
						case 'telephone':
							return 'TicketFieldPhone';
						case 'radio':
							return 'TicketFieldRadio';
						case 'text':
							return 'TicketFieldText';
						case 'url':
							return 'TicketFieldURL';
					}
					return null;
				},
			]
		);

		register_graphql_field(
			'Product',
			'ticketFields',
			[
				'type'        => [ 'list_of' => 'TicketField' ],
				'description' => __( 'Custom ticket fields on this ticket', 'ql-events' ),
				'resolve'     => function( $source ) {
					$ticket_id = $source->ID;

					$meta = tribe( 'tickets-plus.meta' );
					if ( $meta->ticket_has_meta( $ticket_id ) ) {
						return $meta->get_meta_fields_by_ticket( $ticket_id );
					}

					return [];
				},
			]
		);

		register_graphql_field(
			'Event',
			'ticketFields',
			[
				'type'        => [ 'list_of' => 'TicketField' ],
				'description' => __( 'All custom ticket fields on the tickets for this event', 'ql-events' ),
				'resolve'     => function( $source ) {
					$event_id = $source->ID;

					return tribe( 'tickets-plus.meta' )->get_meta_fields_by_event( $event_id );
				},
			]
		);
	}
}
