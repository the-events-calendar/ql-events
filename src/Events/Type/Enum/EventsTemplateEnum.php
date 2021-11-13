<?php
/**
 * Type EventsTemplateEnum
 *
 * @package WPGraphQL\TEC\Events\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Enum;

/**
 * Class - EventsTemplateEnum
 */
class EventsTemplateEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'EventsTemplateEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'Events template.', 'wp-graphql-tec' ),
				'values'      => [
					''        => [
						'name'       => 'EVENTS',
						'value'      => '',
						'desciption' => __( 'Default Events Template', 'wp-graphql-tec' ),
					],
					'default' => [
						'name'        => 'PAGE',
						'value'       => 'default',
						'description' => __( 'Default Page Template', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}

}
