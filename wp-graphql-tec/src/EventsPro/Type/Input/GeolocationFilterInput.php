<?php
/**
 * GraphQL Input Type - GeolocationFilterInput
 *
 * @package WPGraphQL\TEC\EventsPro\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\Input;

/**
 * Class - GeolocationFilterInput
 */
class GeolocationFilterInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'GeolocationFilterInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'The arguments to filter event geographically close to a provided address', 'wp-graphql-tec' ),
				'fields'      => [
					'coordinates' => [
						'type'        => [ 'non_null' => GeolocationCoordinatesInput::$type ],
						'description' => __( 'The geolocation coordinates.', 'wp-graphql-tec' ),
					],
					'distance'    => [
						'type'        => 'Int',
						'description' => __( 'The distance in units from the resolved address. Defaults to 10.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
