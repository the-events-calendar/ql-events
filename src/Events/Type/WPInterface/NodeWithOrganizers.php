<?php
/**
 * GraphQL Interface Type - NodeWithOrganizers
 *
 * @package WPGraphQL\TEC\Events\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\WPInterface;

use GraphQLRelay\Relay;
use WPGraphQL\Registry\TypeRegistry;

/**
 * Class - NodeWithOrganizers
 */
class NodeWithOrganizers {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithOrganizers';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Organizer Fields', 'wp-graphql-tec' ),
				'fields'      => [
					'organizerDatabaseIds' => [
						'type'        => [ 'list_of' => 'Int' ],
						'description' => __( 'The list of organizer database IDs.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => isset( $source->organizerIds ) ? $source->organizerIds : ( tribe_get_organizer_ids( $source->ID ) ?: null ),
					],
					'organizerIds'         => [
						'type'        => [ 'list_of' => 'ID' ],
						'description' => __( 'The list of organizer global IDs.', 'wp-graphql-tec' ),
						'resolve'     => function( $source ) {
							$organizer_ids = isset( $source->organizerIds ) ? $source->organizerIds : tribe_get_organizer_ids( $source->ID );

							if ( empty( $organizer_ids ) ) {
								return null;
							}
							return array_map( fn( $id) => Relay::toGlobalId( 'tribe_organizer', (string) $id ), $organizer_ids );
						},
					],
				],
			]
		);
	}
}
