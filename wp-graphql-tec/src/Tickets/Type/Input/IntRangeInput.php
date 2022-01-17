<?php
/**
 * GraphQL Input Type - IntRangeInput
 *
 * @package WPGraphQL\TEC\Tickets\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Input;

/**
 * Class - IntRangeInput
 */
class IntRangeInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'IntRangeInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'Filters between a min and max integer value', 'wp-graphql-tec' ),
				'fields'      => [
					'min' => [
						'type'        => 'Int',
						'description' => __( 'The minimum number', 'wp-graphql-tec' ),
					],
					'max' => [
						'type'        => 'Int',
						'description' => __( 'The maximum number', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
