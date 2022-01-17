<?php
/**
 * Register *EventConnectionOrderbyEnum
 *
 * @package WPGraphQL\TEC\Events\Type\Enum
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\Enum;

/**
 * Class - EventConnectionOrderbyEnum
 */
class EventConnectionOrderbyEnum {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'EventConnectionOrderbyEnum';

	/**
	 * Registers the GraphQL type
	 */
	public static function register_type() : void {
		register_graphql_enum_type(
			self::$type,
			[
				'description' => __( 'Field to order the connection by', 'wp-graphql-tec' ),
				'values'      => [
					'AUTHOR'         => [
						'value'       => 'post_author',
						'description' => __( 'Order by author', 'wp-graphql-tec' ),
					],
					'COMMENT_COUNT'  => [
						'value'       => 'comment_count',
						'description' => __( 'Order by the number of comments it has acquired', 'wp-graphql-tec' ),
					],
					'DATE'           => [
						'value'       => 'post_date',
						'description' => __( 'Order by publish date', 'wp-graphql-tec' ),
					],
					'EVENT_DATE'     => [
						'value'       => 'event_date',
						'description' => __( 'Order by the event date (in local timezone)', 'wp-graphql-tec' ),
					],
					'EVENT_DATE_UTC' => [
						'value'       => 'event_date_utc',
						'description' => __( 'Order by the event date (in UTC)', 'wp-graphql-tec' ),
					],
					'EVENT_DURATION' => [
						'value'       => 'event_duraton',
						'description' => __( 'Order by the event date (in UTC)', 'wp-graphql-tec' ),
					],
					'IN'             => [
						'value'       => 'post__in',
						'description' => __( 'Preserve the ID order given in the IN array', 'wp-graphql-tec' ),
					],
					'NAME_IN'        => [
						'value'       => 'post_name__in',
						'description' => __( 'Preserve slug order given in the NAME_IN array', 'wp-graphql-tec' ),
					],
					'MENU_ORDER'     => [
						'value'       => 'menu_order',
						'description' => __( 'Order by the menu order value', 'wp-graphql-tec' ),
					],
					'MODIFIED'       => [
						'value'       => 'post_modified',
						'description' => __( 'Order by last modified date', 'wp-graphql-tec' ),
					],
					'PARENT'         => [
						'value'       => 'post_parent',
						'description' => __( 'Order by parent ID', 'wp-graphql-tec' ),
					],
					'ORGANIZER'      => [
						'value'       => 'organizer',
						'description' => __( 'Order by the Organizer name', 'wp-graphql-tec' ),
					],
					'SLUG'           => [
						'value'       => 'post_name',
						'description' => __( 'Order by slug', 'wp-graphql-tec' ),
					],
					'TITLE'          => [
						'value'       => 'post_title',
						'description' => __( 'Order by title', 'wp-graphql-tec' ),
					],
					'VENUE'          => [
						'value'       => 'venue',
						'description' => __( 'Order by the venue name', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}

}
