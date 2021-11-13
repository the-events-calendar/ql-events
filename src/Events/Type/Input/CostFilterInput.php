<?php
/**
 * GraphQL Input Type - CostFilterInput
 *
 * @package WPGraphQL\TEC\Events\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Input;

use WPGraphQL\TEC\Events\Type\Enum\CostOperatorEnum;

/**
 * Class - CostFilterInput
 */
class CostFilterInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'CostFilterInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'Date and timezone values', 'wp-graphql-tec' ),
				'fields'      => [
					'value'    => [
						'type'        => [ 'list_of' => 'Float' ],
						'description' => __( 'The cost to use for the comparison; in the case of `BETWEEN`, `NOT BETWEEN`, `IN` and `NOT IN` operators this value should be an array.', 'wp-graphql-tec' ),
					],
					'operator' => [
						'type'        => CostOperatorEnum::$type,
						'description' => __( 'The comparison operator to use for the comparison. Defaults to `EQUALS`', 'wp-graphql-tec' ),
					],
					'symbol'   => [
						'type'        => 'String',
						'description' => __( 'The desired currency symbol or symbols; this symbol can be a currency ISO code,e.g. "USD" for U.S. dollars, or a currency symbol, e.g. "$". In the latter case results will include any event with the matching currency symbol, this might lead to ambiguous results.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
