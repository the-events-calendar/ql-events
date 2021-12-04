<?php
/**
 * GraphQL Object Type - VenueGeolocation
 *
 * @package WPGraphQL\TEC\EventsPro\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\WPObject;

/**
 * Class - VenueGeolocation
 */
class VenueGeolocation {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'VenueGeolocation';

	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'The venue coordinates.', 'wp-graphql-tec' ),
				'fields'      => [
					'address'                   => [
						'type'        => 'String',
						'description' => __( 'The geolocation address', 'wp-graphql-tec' ),
					],
					'areCoordinatesOverwritten' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether the venue has overwritten coordinates', 'wp-graphql-tec' ),
						'resolve'     => fn ( $source ) => ! empty( $source->overwrite_coordinates ),
					],
					'latitude'                  => [
						'type'        => 'Float',
						'description' => __( 'The venue latitude.', 'wp-graphql-tec' ),
						'resolve'     => fn ( $source) => ! empty( $source->latitude ) ? (float) $source->latitude : null,

					],
					'longitude'                 => [
						'type'        => 'Float',
						'description' => __( 'The venue latitude.', 'wp-graphql-tec' ),
						'resolve'     => fn ( $source) => ! empty( $source->latitude ) ? (float) $source->latitude : null,
					],
				],
			]
		);
	}
}
