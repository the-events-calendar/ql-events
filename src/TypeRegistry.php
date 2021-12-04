<?php
/**
 * Registers TEC types to the schema.
 *
 * @package \WPGraphQL\TEC
 * @since   0.0.1
 */

namespace WPGraphQL\TEC;

use WPGraphQL\Registry\TypeRegistry as GraphQLRegistry;
use WPGraphQL\TEC\Common\TypeRegistry as CommonTypeRegistry;
use WPGraphQL\TEC\Events\TypeRegistry as EventsTypeRegistry;
use WPGraphQL\TEC\EventsPro\TypeRegistry as EventsProTypeRegistry;
use WPGraphQL\TEC\Interfaces\TypeRegistryInterface;
use WPGraphQL\TEC\Tickets\TypeRegistry as TicketsTypeRegistry;

/**
 * Class TypeRegistry
 */
class TypeRegistry implements TypeRegistryInterface {
	/**
	 * Registers QL Events types, connections, unions, and mutations to GraphQL schema
	 *
	 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
	 */
	public static function init( GraphQLRegistry $type_registry ) : void {
		// Initializes module-specific type registries.
		CommonTypeRegistry::init( $type_registry );

		if ( TEC::is_tec_loaded() ) {
			EventsTypeRegistry::init( $type_registry );
		}
		if ( TEC::is_ecp_loaded() ) {
			EventsProTypeRegistry::init( $type_registry );
		}
		if ( TEC::is_et_loaded() ) {
			TicketsTypeRegistry::init( $type_registry );
		}

		/**
		 * Fires before all types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_before_register_types', $type_registry );

		self::register_enums( $type_registry );
		self::register_inputs( $type_registry );
		self::register_interfaces( $type_registry );
		self::register_objects( $type_registry );
		self::register_fields( $type_registry );
		self::register_connections( $type_registry );
		self::register_mutations( $type_registry );

		/**
		 * Fires after all types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_types', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_enums( GraphQLRegistry $type_registry ) : void {
		/**
		 * Fires before all Enum types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_before_register_enums', $type_registry );

		/**
		 * Hook to register common Enum types.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_register_common_enums', $type_registry );

		if ( TEC::is_tec_loaded() ) {
			/**
			 * Hook to register TEC Enum types.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_tec_enums', $type_registry );
		}

		if ( TEC::is_et_loaded() ) {
			/**
			 * Hook to register ET Enum types.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_et_enums', $type_registry );
		}

		/**
		 * Fires after all Enum types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_enums', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_inputs( GraphQLRegistry $type_registry ) : void {
		/**
		 * Fires before all Input types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_before_register_inputs', $type_registry );

		/**
		 * Hook to register common input types.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_register_common_inputs', $type_registry );

		if ( TEC::is_tec_loaded() ) {
			/**
			 * Hook to register TEC input types.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_tec_inputs', $type_registry );
		}

		if ( TEC::is_et_loaded() ) {
			/**
			 * Hook to register ET input types.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_et_inputs', $type_registry );
		}

		/**
		 * Fires after all input types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_inputs', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_interfaces( GraphQLRegistry $type_registry ) : void {
		/**
		 * Fires before all Interface types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_before_register_interfaces', $type_registry );

		/**
		 * Hook to register common Enum types.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_register_common_interfaces', $type_registry );

		if ( TEC::is_tec_loaded() ) {
			/**
			 * Hook to register TEC interface types.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_tec_interfaces', $type_registry );
		}

		if ( TEC::is_et_loaded() ) {
			/**
			 * Hook to register ET interface types.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_et_interfaces', $type_registry );
		}

		/**
		 * Fires after all interface types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_interfaces', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_objects( GraphQLRegistry $type_registry ) : void {
		/**
		 * Fires before all object types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_before_register_objects', $type_registry );

		/**
		 * Hook to register common object types.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_register_common_objects', $type_registry );

		if ( TEC::is_tec_loaded() ) {
			/**
			 * Hook to register TEC object types.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_tec_objects', $type_registry );
		}

		if ( TEC::is_et_loaded() ) {
			/**
			 * Hook to register ET object types.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_et_objects', $type_registry );
		}

		/**
		 * Fires after all object types have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_objects', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_fields( GraphQLRegistry $type_registry ) : void {
		/**
		 * Fires before all fields have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_before_register_fields', $type_registry );

		/**
		 * Hook to register common fields.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_register_common_fields', $type_registry );

		if ( TEC::is_tec_loaded() ) {
			/**
			 * Hook to register TEC fields.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_tec_fields', $type_registry );
		}

		if ( TEC::is_et_loaded() ) {
			/**
			 * Hook to register ET fields.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_et_fields', $type_registry );
		}

		/**
		 * Fires after all fields have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_fields', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_connections( GraphQLRegistry $type_registry ) : void {
		/**
		 * Fires before all connections have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_before_register_connections', $type_registry );

		/**
		 * Hook to register common connections.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_register_common_connections', $type_registry );

		if ( TEC::is_tec_loaded() ) {
			/**
			 * Hook to register TEC connections.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_tec_connections', $type_registry );
		}

		if ( TEC::is_et_loaded() ) {
			/**
			 * Hook to register ET connections.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_et_connections', $type_registry );
		}

		/**
		 * Fires after all connections have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_connections', $type_registry );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register_mutations( GraphQLRegistry $type_registry ) : void {
		/**
		 * Fires before all connections have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_before_register_mutations', $type_registry );

		/**
		 * Hook to register common mutations.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_register_common_mutations', $type_registry );

		if ( TEC::is_tec_loaded() ) {
			/**
			 * Hook to register TEC mutations.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_tec_mutations', $type_registry );
		}

		if ( TEC::is_et_loaded() ) {
			/**
			 * Hook to register ET mutations.
			 *
			 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
			 */
			do_action( 'graphql_tec_register_et_mutations', $type_registry );
		}

		/**
		 * Fires after all connections have been registered.
		 *
		 * @param GraphQLRegistry $type_registry Instance of the WPGraphQL TypeRegistry.
		 */
		do_action( 'graphql_tec_after_register_mutations', $type_registry );
	}
}
