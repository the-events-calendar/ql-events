<?php
/**
 * Registers The Event Calendar types to schema.
 *
 * @package \WPGraphQL\TEC\Events
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events;

use WPGraphQL\Registry\TypeRegistry as GraphQLRegistry;
use WPGraphQL\TEC\Events\Connection;
use WPGraphQL\TEC\Events\Type\Enum;
use WPGraphQL\TEC\Events\Type\Input;
use WPGraphQL\TEC\Events\Type\WPInterface;
use WPGraphQL\TEC\Events\Type\WPObject;
use WPGraphQL\TEC\Interfaces\TypeRegistryInterface;
/**
 * Class - TypeRegistry
 */
class TypeRegistry implements TypeRegistryInterface {

	/**
	 * {@inheritDoc}
	 */
	public static function init( GraphQLRegistry $type_registry ) : void {
		add_action( 'graphql_tec_register_tec_enums', [ __CLASS__, 'register_enums' ] );
		add_action( 'graphql_tec_register_tec_inputs', [ __CLASS__, 'register_inputs' ] );
		add_action( 'graphql_tec_register_tec_interfaces', [ __CLASS__, 'register_interfaces' ] );
		add_action( 'graphql_tec_register_tec_objects', [ __CLASS__, 'register_objects' ] );
		add_action( 'graphql_tec_register_tec_fields', [ __CLASS__, 'register_fields' ] );
		add_action( 'graphql_tec_register_tec_connections', [ __CLASS__, 'register_connections' ] );
		add_action( 'graphql_tec_register_tec_mutations', [ __CLASS__, 'register_mutations' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_enums( GraphQLRegistry $type_registry ) : void {
		Enum\CostOperatorEnum::register_type();
		Enum\CurrencyPositionEnum::register_type();
		Enum\EnabledViewsEnum::register_type();
		Enum\EventsTemplateEnum::register_type();

		/**
		 * Fires after TEC enums have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_tec_enums', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_inputs( GraphQLRegistry $type_registry ) : void {
		Input\CostFilterInput::register_type();
		Input\DateAndTimezoneInput::register_type();
		Input\DateRangeAndTimezoneInput::register_type();

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
		WPInterface\NodeWithVenue::register_interface( $type_registry );
		WPInterface\NodeWithOrganizers::register_interface( $type_registry );
		WPInterface\NodeWithEvent::register_interface( $type_registry );
		/**
		 * Fires after TEC interfaces have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_tec_interfaces', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_objects( GraphQLRegistry $type_registry ) : void {
		WPObject\OrganizerLinkedData::register_type();
		WPObject\VenueLinkedData::register_type();

		/**
		 * Fires after TEC objects have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_tec_objects', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_fields( GraphQLRegistry $type_registry ) : void {
		WPObject\Venue::register_fields();
		WPObject\Event::register_fields();
		WPObject\Organizer::register_fields();
		WPObject\TecSettings::register_fields();

		/**
		 * Fires after TEC fields have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_tec_fields', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_connections( GraphQLRegistry $type_registry ) : void {
		Connection\Events::register_connections();

		/**
		 * Fires after TEC connections have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_tec_connections', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_mutations( GraphQLRegistry $type_registry ) : void {
		/**
		 * Fires after TEC mutations have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_tec_mutations', $type_registry );
	}
}
