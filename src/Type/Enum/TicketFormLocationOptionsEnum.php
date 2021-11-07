<?php
/**
 * Type TicketFormLocationOptionsEnum
 *
 * @package WPGraphQL\TEC\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Type\Enum;

/**
 * Class - TicketFormLocationOptionsEnum
 */
class TicketFormLocationOptionsEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'TicketFormLocationOptionsEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'Location of tickets form.', 'wp-graphql-tec' ),
				'values'      => [
					'tribe_events_single_event_after_the_meta'   => [
						'name'        => 'AFTER_META',
						'value'       => 'tribe_events_single_event_after_the_meta',
						'description' => __( 'Below the event details [default].', 'wp-graphql-tec' ),
					],
					'tribe_events_single_event_before_the_meta'   => [
						'name'        => 'BEFORE_META',
						'value'       => 'tribe_events_single_event_before_the_meta',
						'description' => __( 'Above the event details.', 'wp-graphql-tec' ),
					],
					'tribe_events_single_event_after_the_content'   => [
						'name'        => 'AFTER_CONTENT',
						'value'       => 'tribe_events_single_event_after_the_content',
						'description' => __( 'Below the event description.', 'wp-graphql-tec' ),
					],
					'tribe_events_single_event_before_the_content'   => [
						'name'        => 'BEFORE_CONTENT',
						'value'       => 'tribe_events_single_event_before_the_content',
						'description' => __( 'Below the event description.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
