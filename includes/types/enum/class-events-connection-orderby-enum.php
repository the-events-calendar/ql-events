<?php
/**
 * Enum Type - EventsConnectionOrderbyEnum
 *
 * @package WPGraphQL\QL_Events\Type\WPEnum
 * @since   TBD
 */

namespace WPGraphQL\QL_Events\Type\WPEnum;

/**
 * Class Events_Connection_Orderby_Enum
 */
class Events_Connection_Orderby_Enum {
	/**
	 * Registers type
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_enum_type(
			'EventsConnectionOrderbyEnum',
			[
				'description' => __( 'Triggers for displaying virtual events content.', 'ql-events' ),
				'values'      => [
					'AUTHOR'        => [
						'value'       => 'post_author',
						'description' => __( 'Order by author', 'ql-events' ),
					],
					'TITLE'         => [
						'value'       => 'post_title',
						'description' => __( 'Order by title', 'ql-events' ),
					],
					'SLUG'          => [
						'value'       => 'post_name',
						'description' => __( 'Order by slug', 'ql-events' ),
					],
					'MODIFIED'      => [
						'value'       => 'post_modified',
						'description' => __( 'Order by last modified date', 'ql-events' ),
					],
					'DATE'          => [
						'value'       => 'post_date',
						'description' => __( 'Order by publish date', 'ql-events' ),
					],
					'PARENT'        => [
						'value'       => 'post_parent',
						'description' => __( 'Order by parent ID', 'ql-events' ),
					],
					'IN'            => [
						'value'       => 'post__in',
						'description' => __( 'Preserve the ID order given in the IN array', 'ql-events' ),
					],
					'NAME_IN'       => [
						'value'       => 'post_name__in',
						'description' => __( 'Preserve slug order given in the NAME_IN array', 'ql-events' ),
					],
					'MENU_ORDER'    => [
						'value'       => 'menu_order',
						'description' => __( 'Order by the menu order value', 'ql-events' ),
					],
					'COMMENT_COUNT' => [
						'value'       => 'comment_count',
						'description' => __( 'Order by the number of comments it has acquired', 'ql-events' ),
					],
					'START_DATE'    => [
						'value'       => '_EventStartDateUTC',
						'description' => __( 'Order by the event start date', 'ql-events' ),
					],
					'END_DATE'      => [
						'value'       => '_EventEndDateUTC',
						'description' => __( 'Order by the event end date', 'ql-events' ),
					],
				],
			]
		);
	}
}
