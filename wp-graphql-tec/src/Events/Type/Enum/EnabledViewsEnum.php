<?php
/**
 * Type EnabledViewsEnum
 *
 * @package WPGraphQL\TEC\Events\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Enum;

/**
 * Class - EnabledViewsEnum
 */
class EnabledViewsEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'EnabledViewsEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'Events template.', 'wp-graphql-tec' ),
				'values'      => self::get_values(),
			]
		);
	}

		/**
		 * Generates the Enum values for the config.
		 *
		 * @return array
		 */
	public static function get_values() : array {
		$views = tribe( 'Tribe\Events\Views\V2\Manager' )->get_registered_views();

		if ( false === $views ) {
			return [];
		}

		$values = [];
		foreach ( array_keys( $views ) as $value ) {
			$values[ $value ] = [
				'name'        => strtoupper( str_replace( '-', '_', (string) $value ) ),
				'value'       => $value,
				/* translators: GraphQL event view name */
				'description' => sprintf( __( 'The %s view', 'wp-graphql-tec' ), $value ),
			];
		}
		return $values;
	}

}
