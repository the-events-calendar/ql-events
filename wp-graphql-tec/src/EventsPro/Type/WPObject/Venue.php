<?php
/**
 * Extends the GraphQL Object Type - Venue
 *
 * @package WPGraphQL\TEC\EventsPro\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\WPObject;

/**
 * Class - Venue
 */
class Venue {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'Venue';

	/**
	 * {@inheritDoc}
	 */
	public static function register_fields() : void {
		register_graphql_fields(
			self::$type,
			[
				'geolocation' => [
					'type'        => VenueGeolocation::$type,
					'description' => __( 'The venue geolocation object.', 'wp-graphql-tec' ),
				],
			]
		);
	}
}
