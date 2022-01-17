<?php
/**
 * GraphQL Input Type - CustomFieldRangeFilterInput
 *
 * @package WPGraphQL\TEC\EventsPro\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\Input;

/**
 * Class - CustomFieldRangeFilterInput
 */
class CustomFieldRangeFilterInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'CustomFieldRangeFilterInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'Event Calendar Pro Custom field value range', 'wp-graphql-tec' ),
				'fields'      => [
					'min'  => [
						'type'        => 'String',
						'description' => __( 'The lower limit of the interval. Inclusive.', 'wp-graphql-tec' ),
					],
					'max'  => [
						'type'        => 'String',
						'description' => __( 'The higher limit of the interval. Inclusive.', 'wp-graphql-tec' ),
					],
					'name' => [
						'type'        => 'String',
						'description' => __( 'The custom field name or label.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
