<?php
/**
 * Register TicketIdTypeEnum
 *
 * @see Tribe__Tickets__Global_Stock
 *
 * @package WPGraphQL\TEC\Tickets\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Type\Enum;

/**
 * Class - TicketIdTypeEnum
 */
class TicketIdTypeEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'TicketIdTypeEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'The mode used to indicate how the stock is handled.', 'wp-graphql-tec' ),
				'values'      => [
					'slug'        => [
						'name'       => 'SLUG',
						'value'      => 'slug',
						'desciption' => __( 'Identify a resource by the slug. Available to non-hierarchcial Types where the slug is a unique identifier.', 'wp-graphql-tec' ),
					],
					'id'          => [
						'name'        => 'ID',
						'value'       => 'id',
						'description' => __( 'Identify a resource by the (hashed) Global ID.', 'wp-graphql-tec' ),
					],
					'database_id' => [
						'name'        => 'DATABASE_ID',
						'value'       => 'database_id',
						'description' => __( 'Identify a resource by the Database ID.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
