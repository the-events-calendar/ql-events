<?php
/**
 * GraphQL Interface Type - NodeWithOrganizers
 *
 * @package WPGraphQL\TEC\Events\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\WPInterface;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Events\Data\OrganizerHelper;
use WPGraphQL\TEC\Events\Type\WPObject\Organizer;

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
				'connections' => [
					'organizers' => [
						'toType'  => Organizer::$type,
						'resolve' => function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
							if ( empty( $source->organizerIds ) ) {
								return null;
							}

							$args['where']['post__in'] = $source->organizerIds;

							return OrganizerHelper::resolve_connection( $source, $args, $context, $info, 'tribe_organizer' );
						},
					],
				],
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
