<?php
/**
 * GraphQL Interface Type - NodeWithEvent
 *
 * @package WPGraphQL\TEC\Events\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\WPInterface;

use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Events\Data\EventHelper;
use WPGraphQL\TEC\Events\Type\WPObject\Event;

/**
 * Class - NodeWithEvent
 */
class NodeWithEvent {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithEvent';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Event fields', 'wp-graphql-tec' ),
				'fields'      => [
					'event'           => [
						'type'        => Event::$type,
						'description' => __( 'The Event.', 'wp-graphql-tec' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							if ( empty( $source->eventId ) ) {
								return null;
							}

							return EventHelper::resolve_object( $source->eventId, $context );
						},
					],
					'eventDatabaseId' => [
						'type'        => 'Int',
						'description' => __( 'The Event database ID.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => ! empty( $source->eventId ) ? $source->eventId : null,
					],
					'eventId'         => [
						'type'        => 'ID',
						'description' => __( 'The Event global ID.', 'wp-graphql-tec' ),
						'resolve'     => function( $source ) {
							if ( empty( $source->eventId ) ) {
								return null;
							}

							return Relay::toGlobalId( 'tribe_events', (string) $source->eventId );
						},
					],
				],
			]
		);
	}
}
