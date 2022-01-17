<?php
/**
 * Registers Events Calendar Pro types to schema.
 *
 * @package \WPGraphQL\TEC\EventsPro
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro;

use WPGraphQL\Registry\TypeRegistry as GraphQLRegistry;
use WPGraphQL\TEC\Interfaces\TypeRegistryInterface;
use WPGraphQL\TEC\EventsPro\Connection;
use WPGraphQL\TEC\EventsPro\Type\Enum;
use WPGraphQL\TEC\EventsPro\Type\Input;
use WPGraphQL\TEC\EventsPro\Type\WPInterface;
use WPGraphQL\TEC\EventsPro\Type\WPObject;
use WPGraphQL\TEC\EventsPro\Mutation;

/**
 * Class - TypeRegistry
 */
class TypeRegistry implements TypeRegistryInterface {

	/**
	 * {@inheritDoc}
	 */
	public static function init( GraphQLRegistry $type_registry ) : void {
		add_action( 'graphql_tec_after_register_tec_enums', [ __CLASS__, 'register_enums' ] );
		add_action( 'graphql_tec_after_register_tec_inputs', [ __CLASS__, 'register_inputs' ] );
		add_action( 'graphql_tec_after_register_tec_interfaces', [ __CLASS__, 'register_interfaces' ] );
		add_action( 'graphql_tec_after_register_tec_objects', [ __CLASS__, 'register_objects' ] );
		add_action( 'graphql_tec_after_register_tec_fields', [ __CLASS__, 'register_fields' ] );
		add_action( 'graphql_tec_after_register_tec_connections', [ __CLASS__, 'register_connections' ] );
		add_action( 'graphql_tec_after_register_tec_mutations', [ __CLASS__, 'register_mutations' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_enums( GraphQLRegistry $type_registry ) : void {
		Enum\DistanceUnitEnum::register_type();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_inputs( GraphQLRegistry $type_registry ) : void {
		Input\CustomFieldFilterInput::register_type();
		Input\CustomFieldRangeFilterInput::register_type();
		Input\GeolocationCoordinatesInput::register_type();
		Input\GeolocationFilterInput::register_type();
		Input\NearFilterInput::register_type();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_interfaces( GraphQLRegistry $type_registry ) : void {
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_objects( GraphQLRegistry $type_registry ) : void {
		WPObject\VenueGeolocation::register_type();
		WPObject\RecurrenceDetails::register_type();
		WPObject\CustomFields::register_type();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_fields( GraphQLRegistry $type_registry ) : void {
		WPObject\Event::register_fields();
		WPObject\Venue::register_fields();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_connections( GraphQLRegistry $type_registry ) : void {
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_mutations( GraphQLRegistry $type_registry ) : void {
	}
}
