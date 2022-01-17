<?php
/**
 * Type PaymentGatewaysEnum
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

/**
 * Class - PaymentGatewaysEnum
 */
class PaymentGatewaysEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'PaymentGatewaysEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The payment gateways registered to Event Tickets', 'wp-graphql-tec' ),
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
		$payment_gateways = tribe( 'TEC\Tickets\Commerce\Gateways\Manager' )->get_gateways();
		$values           = [];

		foreach ( array_keys( $payment_gateways ) as $name ) {
			$values[ $name ] = [
				'name'        => strtoupper( (string) $name ),
				'value'       => $name,
				/* translators: payment gateway */
				'description' => sprintf( __( 'The %s payment gateway', 'wp-graphql-tec' ), $name ),
			];
		}
		return $values;
	}
}
