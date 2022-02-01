<?php
namespace WPGraphQL\QL_Events\Type\WPInterface;

class Ticket_Field {

	/**
	 * Register the Node interface
	 *
	 * @return void
	 */
	public static function register_type() {
		register_graphql_interface_type(
			'TicketField',
			array(
				'description' => __( 'A ticket custom field', 'wp-graphql' ),
				'fields'      => array(
					'type'      => array(
						'type'        => 'String',
						'description' => __( 'Field type', 'wp-graphql' ),
						'resolve'     => function( $field ) {
							return $field->type;
						},
					),
					'label'      => array(
						'type'        => 'String',
						'description' => __( 'Field Label', 'wp-graphql' ),
						'resolve'     => function( $field ) {
							return $field->label;
						},
					),
					'description'      => array(
						'type'        => 'String',
						'description' => __( 'Field Description', 'wp-graphql' ),
						'resolve'     => function( $field ) {
							return $field->description;
						},
					),
					'required'      => array(
						'type'        => 'Boolean',
						'description' => __( 'Is this field required?', 'wp-graphql' ),
						'resolve'     => function( $field ) {
							return 'on' === $field->required;
						},
					),
				),
				'resolveType' => function ( $field ) {
					switch( $field->type ) {
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
			)
		);

		register_graphql_field(
			'Product',
			'ticketFields',
			array(
				'type'        => array( 'list_of' => 'TicketField' ),
				'description' => __( 'Custom ticket fields on this ticket', 'ql-events' ),
				'resolve'     => function( $source ) {
					$ticket_id = $source->ID;

					$meta = tribe( 'tickets-plus.meta' );
					if ( $meta->ticket_has_meta( $ticket_id ) ) {
						return $meta->get_meta_fields_by_ticket( $ticket_id );
					}

					return array();
				}
			)
		);

		register_graphql_field(
			'Event',
			'ticketFields',
			array(
				'type'        => array( 'list_of' => 'TicketField' ),
				'description' => __( 'All custom ticket fields on the tickets for this event', 'ql-events' ),
				'resolve'     => function( $source ) {
					$event_id = $source->ID;

					return tribe( 'tickets-plus.meta' )->get_meta_fields_by_event( $event_id );
				}
			)
		);
	}
}
