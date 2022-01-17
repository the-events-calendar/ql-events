<?php
/**
 * Interface for classes containing WordPress action/filter hooks.
 *
 * @package \WPGraphQL\TEC
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Interfaces;

use WPGraphQL\Registry\TypeRegistry;

/**
 * Interface - TypeRegistryInterface
 */
interface TypeRegistryInterface {

	/**
	 * Registers the types, connections, unions, and mutations to GraphQL schema
	 *
	 * @param TypeRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
	 */
	public static function init( TypeRegistry $type_registry ) : void;

	/**
	 * Fires hooks responsible for registering Enum types.
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_enums( TypeRegistry $type_registry ) : void;

	/**
	 * Fires hooks responsible for registering Input types.
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_inputs( TypeRegistry $type_registry ) : void;

	/**
	 * Fires hooks responsible for registering Interface types.
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interfaces( TypeRegistry $type_registry ) : void;

	/**
	 * Fires hooks responsible for registering Object types.
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_objects( TypeRegistry $type_registry ) : void;

	/**
	 * Fires hooks responsible for registering fields to GraphQL types.
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_fields( TypeRegistry $type_registry ) : void;

	/**
	 * Fires hooks responsible for registering fields to GraphQL types.
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_connections( TypeRegistry $type_registry ) : void;

	/**
	 * Fires hooks responsible for registering mutations to GraphQL types.
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_mutations( TypeRegistry $type_registry ) : void;
}
