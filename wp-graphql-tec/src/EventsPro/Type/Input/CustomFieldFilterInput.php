<?php
/**
 * GraphQL Input Type - CustomFieldFilterInput
 *
 * @package WPGraphQL\TEC\EventsPro\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\Input;

/**
 * Class - CustomFieldFilterInput
 */
class CustomFieldFilterInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'CustomFieldFilterInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'Event Calendar Pro Custom field values', 'wp-graphql-tec' ),
				'fields'      => [
					'name'  => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'The custom field name or label.', 'wp-graphql-tec' ),
					],
					'value' => [
						'type'        => 'String',
						'description' => __( 'A LIKE-compatible string or a regular expression will be used for LIKE or REGEXP comparisons. Use a regular expression to get exact matches. The limitations of SQL REGEXP syntax apply (e.g not modifiers.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
