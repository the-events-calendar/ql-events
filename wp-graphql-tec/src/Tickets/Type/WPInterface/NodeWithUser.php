<?php
/**
 * GraphQL Interface Type - NodeWithUser
 *
 * @package WPGraphQL\TEC\Tickets\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\WPInterface;

use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Tickets\Data\TicketHelper;
use WPGraphQL\TEC\Tickets\Data\VenueHelper;
use WPGraphQL\TEC\Tickets\Type\WPObject\Venue;

/**
 * Class - NodeWithUser
 */
class NodeWithUser {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithUser';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'User fields', 'wp-graphql-tec' ),
				'fields'      => [
					'user'           => [
						'type'        => 'User',
						'description' => __( 'The user.', 'wp-graphql-tec' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							if ( $source->userId === $source->ID ) {
								return null;
							}

							return DataSource::resolve_user( $source->userId, $context );
						},
					],
					'userDatabaseId' => [
						'type'        => 'Int',
						'description' => __( 'The user database ID.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => $source->userId ?? null,
					],
					'userId'         => [
						'type'        => 'ID',
						'description' => __( 'The user global ID.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => $source->userId ? Relay::toGlobalId( 'user', (string) $source->userId ) : null,
					],
				],
			]
		);
	}
}
