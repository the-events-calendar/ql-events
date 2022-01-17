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
							if ( empty( $source->organizerDatabaseIds ) ) {
								return null;
							}

							$args['where']['post__in'] = $source->organizerDatabaseIds;

							return OrganizerHelper::resolve_connection( $source, $args, $context, $info, 'tribe_organizer' );
						},
					],
				],
				'fields'      => [
					'organizerDatabaseIds' => [
						'type'        => [ 'list_of' => 'Int' ],
						'description' => __( 'The list of organizer database IDs.', 'wp-graphql-tec' ),
					],
					'organizerIds'         => [
						'type'        => [ 'list_of' => 'ID' ],
						'description' => __( 'The list of organizer global IDs.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => ! empty( $source->organizerDatabaseIds ) ? array_map( fn( $id) => Relay::toGlobalId( 'tribe_organizer', (string) $id ), $source->organizerDatabaseIds ) : null,
					],
				],
			]
		);
	}
}
