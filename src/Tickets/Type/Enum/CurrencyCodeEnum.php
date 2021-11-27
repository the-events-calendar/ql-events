<?php
/**
 * Type CurrencyCodeEnum
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

use WPGraphQL\TEC\TEC;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - CurrencyCodeEnum
 */
class CurrencyCodeEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'CurrencyCodeEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The 3-digit ISO currency code', 'wp-graphql-tec' ),
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
		$currency_codes = tribe( 'tickets.commerce.currency' )->generate_currency_code_options();
		$values         = [];

		foreach ( $currency_codes as $name => $description ) {
			$values[ $name ] = [
				'name'        => $name,
				'value'       => $name,
				'description' => $description,
			];
		}
		return $values;
	}
}
