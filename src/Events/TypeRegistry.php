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
		add_action( 'graphql_tec_register_tec_interfaces', [ __CLASS__, 'register_interfaces' ] );
		add_action( 'graphql_tec_register_tec_objects', [ __CLASS__, 'register_objects' ] );
		add_action( 'graphql_tec_register_tec_fields', [ __CLASS__, 'register_fields' ] );
		add_action( 'graphql_tec_register_tec_connections', [ __CLASS__, 'register_connections' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_enums( GraphQLRegistry $type_registry ) : void {
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
	public static function register_interfaces( GraphQLRegistry $type_registry ) : void {
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
		WPObject\EventLinkedData::register_type();
		WPObject\OrganizerLinkedData::register_type();
		WPObject\VenueCoordinates::register_type();
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
		Connection\Organizers::register_connections();
		Connection\Venues::register_connections();

		/**
		 * Fires after TEC connections have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_tec_connections', $type_registry );
	}
}
