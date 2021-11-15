<?php
/**
 * GraphQL Interface Type - NodeWithVenue
 *
 * @package WPGraphQL\TEC\Events\Type\WPInterface;
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\WPInterface;

use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Registry\TypeRegistry;
use WPGraphQL\TEC\Events\Data\VenueHelper;
use WPGraphQL\TEC\Events\Type\WPObject\Venue;

/**
 * Class - NodeWithVenue
 */
class NodeWithVenue {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NodeWithVenue';

	/**
	 * Registers GraphQL Interface
	 *
	 * @param TypeRegistry $type_registry .
	 */
	public static function register_interface( TypeRegistry &$type_registry ): void {
		register_graphql_interface_type(
			self::$type,
			[
				'description' => __( 'Venue fields', 'wp-graphql-tec' ),
				'fields'      => [
					'venue'           => [
						'type'        => Venue::$type,
						'description' => __( 'The venue.', 'wp-graphql-tec' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							$venue_id = ! empty( $source->venueId ) ? $source->venueId : tribe_get_venue_id( $source->ID );

							return VenueHelper::resolve_object( $venue_id, $context );
						},
					],
					'venueDatabaseId' => [
						'type'        => 'Int',
						'description' => __( 'The venue database ID.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) => ! empty( $source->venueId ) ? $source->venueId : tribe_get_venue_id( $source->ID ),
					],
					'venueId'         => [
						'type'        => 'ID',
						'description' => __( 'The venue global ID.', 'wp-graphql-tec' ),
						'resolve'     => function( $source ) {
							$venue_id = ! empty( $source->venueId ) ? $source->venueId : tribe_get_venue_id( $source->ID );

							return Relay::toGlobalId( 'tribe_venue', (string) $venue_id );
						},
					],
				],
			]
		);
	}
}
