<?php
/**
 * Registers Event Tickets types to schema.
 *
 * @package \WPGraphQL\TEC\Tickets
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets;

use WPGraphQL\Registry\TypeRegistry as GraphQLRegistry;
use WPGraphQL\TEC\Interfaces\TypeRegistryInterface;
use WPGraphQL\TEC\Tickets\Connection;
use WPGraphQL\TEC\Tickets\Type\Enum;
use WPGraphQL\TEC\Tickets\Type\Input;
use WPGraphQL\TEC\Tickets\Type\WPInterface;
use WPGraphQL\TEC\Tickets\Type\WPObject;
use WPGraphQL\TEC\Tickets\Mutation;

/**
 * Class - TypeRegistry
 */
class TypeRegistry implements TypeRegistryInterface {

	/**
	 * {@inheritDoc}
	 */
	public static function init( GraphQLRegistry $type_registry ) : void {
		add_action( 'graphql_tec_register_et_enums', [ __CLASS__, 'register_enums' ] );
		add_action( 'graphql_tec_register_et_inputs', [ __CLASS__, 'register_inputs' ] );
		add_action( 'graphql_tec_register_et_interfaces', [ __CLASS__, 'register_interfaces' ] );
		add_action( 'graphql_tec_register_et_objects', [ __CLASS__, 'register_objects' ] );
		add_action( 'graphql_tec_register_et_fields', [ __CLASS__, 'register_fields' ] );
		add_action( 'graphql_tec_register_et_connections', [ __CLASS__, 'register_connections' ] );
		add_action( 'graphql_tec_register_et_mutations', [ __CLASS__, 'register_mutations' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_enums( GraphQLRegistry $type_registry ) : void {
		Enum\AttendeeTypeEnum::register_type();
		Enum\AttendeeOptoutStatusEnum::register_type();
		Enum\CurrencyCodeEnum::register_type();
		Enum\PaymentGatewaysEnum::register_type();
		Enum\StockHandlingOptionsEnum::register_type();
		Enum\StockModeEnum::register_type();
		Enum\TicketIdTypeEnum::register_type();
		Enum\TicketTypeEnum::register_type();
		Enum\TicketFormLocationOptionsEnum::register_type();
		Enum\OrderTypeEnum::register_type();
		Enum\OrderStatusEnum::register_type();

		/**
		 * Fires after ET enums have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_et_enums', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_inputs( GraphQLRegistry $type_registry ) : void {
		Input\IntRangeInput::register_type();
		Input\AttendeeInput::register_type();

		/**
		 * Fires after TEC enums have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_tec_inputs', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_interfaces( GraphQLRegistry $type_registry ) : void {
		WPInterface\Attendee::register_interface( $type_registry );
		WPInterface\NodeWithAttendees::register_interface( $type_registry );
		WPInterface\NodeWithOrder::register_interface( $type_registry );
		WPInterface\NodeWithTicket::register_interface( $type_registry );
		WPInterface\NodeWithTickets::register_interface( $type_registry );
		WPInterface\NodeWithUser::register_interface( $type_registry );
		WPInterface\PurchasableTicket::register_interface( $type_registry );
		WPInterface\Ticket::register_interface( $type_registry );
		WPInterface\Order::register_interface( $type_registry );

		/**
		 * Fires after ET interfaces have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_et_interfaces', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_objects( GraphQLRegistry $type_registry ) : void {
		WPObject\OffersLinkedData::register_type();
		WPObject\OrderItem::register_type();
		/**
		 * Fires after ET objects have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_et_objects', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_fields( GraphQLRegistry $type_registry ) : void {
		WPObject\RsvpTicket::register_fields();
		WPObject\TecSettings::register_fields();

		/**
		 * Fires after ET fields have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_et_fields', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_connections( GraphQLRegistry $type_registry ) : void {
		Connection\Tickets::register_connections();
		Connection\Attendees::register_connections();
		Connection\Orders::register_connections();

		/**
		 * Fires after ET connections have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_et_connections', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_mutations( GraphQLRegistry $type_registry ) : void {
		Mutation\CreateRsvp::register_mutation();
		Mutation\UpdateRsvp::register_mutation();

		/**
		 * Fires after TEC mutations have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_et_mutations', $type_registry );
	}
}
