<?php
/**
 * GraphQL Input Type - EventConnectionOrderbyInput
 *
 * @package WPGraphQL\TEC\Events\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Input;

use WPGraphQL\TEC\Events\Type\Enum\EventConnectionOrderbyEnum;

/**
 * Class - EventConnectionOrderbyInput
 */
class EventConnectionOrderbyInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'EventConnectionOrderbyInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'Options for ordering the connection', 'wp-graphql-tec' ),
				'fields'      => [
					'field' => [
						'type'        => [
							'non_null' => EventConnectionOrderbyEnum::$type,
						],
						'description' => __( 'The field to order the connection by', 'wp-graphql-tec' ),
					],
					'order' => [
						'type'        => [
							'non_null' => 'OrderEnum',
						],
						'description' => __( 'Possible directions in which to order a list of items', 'wp-graphql-tec' ),
					],
				],
			],
		);
	}
}
