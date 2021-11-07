<?php
/**
 * Type EnabledViewsEnum
 *
 * @package WPGraphQL\TEC\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Type\Enum;

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
				'values'      => [
					'day'   => [
						'name'        => 'DAY',
						'value'       => 'day',
						'description' => __( 'Day view.', 'wp-graphql-tec' ),
					],
					'list'  => [
						'name'       => 'LIST',
						'value'      => 'list',
						'desciption' => __( 'List view.', 'wp-graphql-tec' ),
					],
					'month' => [
						'name'        => 'MONTH',
						'value'       => 'month',
						'description' => __( 'Month view.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}

}
