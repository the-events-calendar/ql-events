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
	 * Returns true if the Events Pro fields, types, queries, and mutations can be loaded.
	 *
	 * @since 0.3.0
	 *
	 * @return bool
	 */
	public function load_events_pro_schema() {
		return QL_Events::is_events_pro_support_enabled()
			&& QL_Events::is_events_pro_active();
	}

	/**
	 * Returns true if the Event Tickets fields, types, queries, and mutations can be loaded.
	 *
	 * @since 0.3.0
	 *
	 * @return bool
	 */
	public function load_event_tickets_schema() {
		return QL_Events::is_event_tickets_support_enabled()
			&& QL_Events::is_event_tickets_active();
	}

	/**
	 * Returns true if the Event Tickets Plus fields, types, queries, and mutations can be loaded.
	 *
	 * @since 0.3.0
	 *
	 * @return bool
	 */
	public function load_event_tickets_plus_schema() {
		return QL_Events::is_event_tickets_plus_support_enabled()
			&& QL_Events::is_event_tickets_plus_active()
			&& QL_Events::is_woographql_active();
	}

	/**
	 * Returns true if the Events Virtual fields, types, queries, and mutations can be loaded.
	 *
	 * @since 0.3.0
	 *
	 * @return bool
	 */
	public function load_events_virtual_schema() {
		return QL_Events::is_events_virtual_support_enabled()
			&& QL_Events::is_events_virtual_active();
	}

	/**
	 * Registers QL Events types, connections, unions, and mutations to GraphQL schema
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function init() {
		// TEC core fields, types, queries, and mutations.

		// objects/fields.
		Type\WPObject\Organizer_Linked_Data_Type::register();
		Type\WPObject\Venue_Linked_Data_Type::register();
		Type\WPObject\Event_Linked_Data_Type::register();
		Type\WPObject\Event_Type::register_fields();
		Type\WPObject\Organizer_Type::register_fields();
		Type\WPObject\Venue_Type::register_fields();

		// connections.
		Connection\Organizers::register_connections();

		// Register Events Pro fields, types, queries, and mutations.
		if ( $this->load_events_pro_schema() ) {
			// fields.
			Type\WPObject\Event_Type::register_pro_fields();
		}

		// Register Event Tickets fields, types, queries, and mutations.
		if ( $this->load_event_tickets_schema() ) {
			// inputs.
			if ( ! QL_Events::is_woographql_active() ) {
				Type\WPInputObject\Meta_Data_Input::register();
			}

			// interfaces.
			Type\WPInterface\Ticket_Interface::register_interface();
			Type\WPInterface\Attendee_Interface::register_interface();
			Type\WPInterface\Order_Interface::register_interface();

			// objects/fields.
			Type\WPObject\PayPalAttendee_Type::register_fields();
			Type\WPObject\PayPalOrder_Type::register_fields();
			Type\WPObject\PayPalTicket_Type::register_fields();
			Type\WPObject\RSVPAttendee_Type::register_fields();
			Type\WPObject\RSVPTicket_Type::register_fields();
			if ( ! QL_Events::is_woographql_active() ) {
				Type\WPObject\Meta_Data_Type::register();
			}

			// connections.
			Connection\Tickets::register_connections();
			Connection\Attendees::register_connections();

			// mutations.
			Mutation\Register_Attendee::register_mutation();
			Mutation\Update_Attendee::register_mutation();
		}

		// Register Event Tickets Plus fields, types, queries, and mutations.
		if ( $this->load_event_tickets_plus_schema() ) {
			// interfaces.
			Type\WPInterface\Ticket_Field::register_type();

			// objects/fields.
			Type\WPObject\Ticket_Field\Birthdate::register();
			Type\WPObject\Ticket_Field\Checkbox::register();
			Type\WPObject\Ticket_Field\Date::register();
			Type\WPObject\Ticket_Field\Dropdown::register();
			Type\WPObject\Ticket_Field\Email::register();
			Type\WPObject\Ticket_Field\Phone::register();
			Type\WPObject\Ticket_Field\Radio::register();
			Type\WPObject\Ticket_Field\Text::register();
			Type\WPObject\Ticket_Field\URL::register();
			Type\WPObject\WooTicket_Type::register_to_ticket_interface();
			Type\WPObject\WooOrder_Type::register_to_order_interface();
			Type\WPObject\WooOrder_Type::register_fields();
			Type\WPObject\WooAttendee_Type::register_to_attendee_interface();
			Type\WPObject\WooAttendee_Type::register_fields();
			Type\WPObject\Ticket_Linked_Data_Type::register();

			// connections.
			Connection\Tickets_Plus::register_available_plus_ticket_types();
			Connection\Tickets_Plus::register_connections();
		}

		// Register Events Virtual types, queries, and mutations.
		if ( $this->load_events_virtual_schema() ) {
			// enums.
			Type\WPEnum\Events_Virtual_Show_Embed_At_Enum::register();
			Type\WPEnum\Events_Virtual_Show_Embed_To_Enum::register();

			// objects.
			Type\WPObject\Event_Type::register_virtual_fields();
		}
	}
}
