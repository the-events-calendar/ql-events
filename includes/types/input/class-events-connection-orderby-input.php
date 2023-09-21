<?php
/**
 * WPInputObjectType - EventsConnectionOrderbyInput
 *
 * @package WPGraphQL\QL_Events\Type\WPInputObject
 * @since   0.3.1
 */

namespace WPGraphQL\QL_Events\Type\WPInputObject;

/**
 * Class Events_Connection_Orderby_Input
 */
class Events_Connection_Orderby_Input {

	/**
	 * Registers type
	 *
	 * @since 0.3.1
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_input_type(
			'EventsConnectionOrderbyInput',
			[
				'description' => __( 'Options for ordering the connection', 'ql-events' ),
				'fields'      => [
					'field' => [
						'type'        => [
							'non_null' => 'EventsConnectionOrderbyEnum',
						],
						'description' => __( 'The field to order the connection by', 'ql-events' ),
					],
					'order' => [
						'type'        => [
							'non_null' => 'OrderEnum',
						],
						'description' => __( 'Possible directions in which to order a list of items', 'ql-events' ),
					],
				],
			]
		);
	}
}
