<?php
/**
 * Registers TEC types to the schema.
 *
 * @package \WPGraphQL\QL_Events
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events;

/**
 * Class Type_Registry
 */
class Type_Registry {
	/**
	 * Registers QL Events types, connections, unions, and mutations to GraphQL schema
	 *
	 * @param \WPGraphQL\Registry\TypeRegistry $type_registry  Instance of the WPGraphQL TypeRegistry.
	 */
	public function init( \WPGraphQL\Registry\TypeRegistry $type_registry ) {
		// ET Interfaces.
		Type\WPInterface\Ticket_Interface::register_interface( $type_registry );

		// TEC Object fields.
		Type\WPObject\Event_Type::register_fields();
		Type\WPObject\Event_Linked_Data_Type::register();
		Type\WPObject\Organizer_Type::register_fields();
		Type\WPObject\Organizer_Linked_Data_Type::register();
		Type\WPObject\Venue_Type::register_fields();
		Type\WPObject\Venue_Linked_Data_Type::register();

		// TEC Connections.
		Connection\Organizers::register_connections();

		// Register type fields if Event Tickets in installed and loaded.
		if ( QL_Events::is_ticket_events_loaded() ) {
			Type\WPObject\PayPalAttendee_Type::register_fields();
			Type\WPObject\PayPalOrder_Type::register_fields();
			Type\WPObject\PayPalTicket_Type::register_fields();
			Type\WPObject\RSVPAttendee_Type::register_fields();
			Type\WPObject\RSVPTicket_Type::register_fields();

			// Event Tickets Connections.
			Connection\Tickets::register_connections();
		}

		if ( QL_Events::is_ticket_events_plus_loaded() ) {
			Type\WPObject\WooAttendee_Type::register_fields();
			Type\WPObject\Ticket_Linked_Data_Type::register();

			// Custom ticket meta.
			Type\WPInterface\Ticket_Field::register_type();
			Type\WPObject\Ticket_Field\Birthdate::register();
			Type\WPObject\Ticket_Field\Checkbox::register();
			Type\WPObject\Ticket_Field\Date::register();
			Type\WPObject\Ticket_Field\Dropdown::register();
			Type\WPObject\Ticket_Field\Email::register();
			Type\WPObject\Ticket_Field\Phone::register();
			Type\WPObject\Ticket_Field\Radio::register();
			Type\WPObject\Ticket_Field\Text::register();
			Type\WPObject\Ticket_Field\URL::register();
		}
	}
}
