<?php
/**
 * GraphQL Object Type - VenueCoordinates
 *
 * @package WPGraphQL\TEC\Type\Object
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Type\WPObject;

/**
 * Class - VenueCoordinates
 */
class VenueCoordinates {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'VenueCoordinates';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'The venue coordinates.', 'wp-graphql-tec' ),
				'fields'      => [
					'latitude'  => [
						'type'        => 'Float',
						'description' => __( 'The venue latitude.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : ?float => $source['lat'] ?: null,
					],
					'longitude' => [
						'type'        => 'Float',
						'description' => __( 'The venue latitude.', 'wp-graphql-tec' ),
						'resolve'     => fn( $source ) : ?float => $source['lng'] ?: null,
					],
				],
			]
		);
	}
}
