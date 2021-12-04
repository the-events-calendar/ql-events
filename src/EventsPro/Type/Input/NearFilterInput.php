<?php
/**
 * GraphQL Input Type - NearFilterInput
 *
 * @package WPGraphQL\TEC\EventsPro\Type\Input
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Type\Input;

/**
 * Class - NearFilterInput
 */
class NearFilterInput {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'NearFilterInput';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_input_type(
			self::$type,
			[
				'description' => __( 'The arguments to filter event geographically close to a provided address', 'wp-graphql-tec' ),
				'fields'      => [
					'address'  => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'The address string', 'wp-graphql-tec' ),
					],
					'distance' => [
						'type'        => 'Int',
						'description' => __( 'The distance in units from the resolved address. Defaults to 10.', 'wp-graphql-tec' ),
					],
				],
			]
		);
	}
}
