<?php
/**
 * Register *CostOperatorEnum
 *
 * @package WPGraphQL\TEC\Events\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Enum;

/**
 * Class - CostOperatorEnum
 */
class CostOperatorEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'CostOperatorEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The position of the currency symbol.', 'wp-graphql-tec' ),
				'values'      => [
					'='           => [
						'name'       => 'EQUALS',
						'value'      => '=',
						'desciption' => __( '`=` operator', 'wp-graphql-tec' ),
					],
					'<'           => [
						'name'       => 'LESS_THAN',
						'value'      => '<',
						'desciption' => __( '`<` operator', 'wp-graphql-tec' ),
					],
					'<='          => [
						'name'       => 'LESS_THAN_OR_EQUALS',
						'value'      => '<=',
						'desciption' => __( '`<=` operator', 'wp-graphql-tec' ),
					],
					'>'           => [
						'name'       => 'GREATER_THAN',
						'value'      => '>',
						'desciption' => __( '`>` operator', 'wp-graphql-tec' ),
					],
					'>='          => [
						'name'       => 'GREATER_THAN_OR_EQUALS',
						'value'      => '>=',
						'desciption' => __( '`>=` operator', 'wp-graphql-tec' ),
					],
					'BETWEEN'     => [
						'name'        => 'BETWEEN',
						'value'       => 'BETWEEN',
						'description' => __( '`BETWEEN` operator`', 'wp-graphql-tec' ),
					],
					'NOT BETWEEN' => [
						'name'        => 'NOT_BETWEEN',
						'value'       => 'NOT BETWEEN',
						'description' => __( '`NOT BETWEEN` operator`', 'wp-graphql-tec' ),
					],
					'IN'          => [
						'name'        => 'IN',
						'value'       => 'IN',
						'description' => __( '`IN` operator`', 'wp-graphql-tec' ),
					],
					'NOT IN'      => [
						'name'        => 'NOT_IN',
						'value'       => 'NOT IN',
						'description' => __( '`NOT IN` operator`', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}

}
