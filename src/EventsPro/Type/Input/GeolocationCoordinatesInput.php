<?php
/**
 * GraphQL Input Type - GeolocationCoordinatesInput
 *
 * @package WPGraphQL\TEC\EventsPro\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\Input;

/**
 * Class - GeolocationCoordinatesInput
 */
class GeolocationCoordinatesInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'GeolocationCoordinatesInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'The arguments to filter event geographically close to a provided address', 'wp-graphql-tec' ),
				'fields'      => [
					'latitude' => [
						'type'        => 'Float',
						'description' => __( 'The center latitude.', 'wp-graphql-tec' ),
					],
					'longitue' => [
						'type'        => 'Float',
						'description' => __( 'The center longitude.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
