<?php
/**
 * Register AttendeeOptoutStatusEnum
 *
 * @see Tribe__Tickets__Global_Stock
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

/**
 * Class - AttendeeOptoutStatusEnum
 */
class AttendeeOptoutStatusEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'AttendeeOptoutStatusEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'Whether the user has opted out of being shown publicly.', 'wp-graphql-tec' ),
				'values'      => [
					'any' => [
						'name'       => 'ANY',
						'value'      => 'any',
						'desciption' => __( 'Any optout status.', 'wp-graphql-tec' ),
					],
					'no'  => [
						'name'        => 'NO',
						'value'       => 'no',
						'description' => __( 'The user can be shown publicly', 'wp-graphql-tec' ),
					],
					'yes' => [
						'name'        => 'YES',
						'value'       => 'yes',
						'description' => __( 'The user has opted out of being shown publicly.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
