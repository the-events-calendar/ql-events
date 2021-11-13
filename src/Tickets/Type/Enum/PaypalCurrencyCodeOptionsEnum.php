<?php
/**
 * Type PaypalCurrencyCodeOptionsEnum
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

use WPGraphQL\TEC\TEC;

/**
 * Class - PaypalCurrencyCodeOptionsEnum
 */
class PaypalCurrencyCodeOptionsEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'PaypalCurrencyCodeOptionsEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'Location of tickets form.', 'wp-graphql-tec' ),
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
		$codes  = tribe( 'tickets.commerce.currency' )->generate_currency_code_options();
		$values = [];

		foreach ( $codes as $value => $description ) {
			$values[ $value ] = [
				'name'        => $value,
				'value'       => $value,
				'description' => $description,
			];
		}
		return $values;
	}
}
