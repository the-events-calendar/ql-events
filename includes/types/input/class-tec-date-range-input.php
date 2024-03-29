<?php
/**
 * WPInputObjectType - TECDateRangeInput
 *
 * @package WPGraphQL\QL_Events\Type\WPInputObject
 * @since   0.3.1
 */

namespace WPGraphQL\QL_Events\Type\WPInputObject;

/**
 * Class TEC_Date_Range_Input
 */
class TEC_Date_Range_Input {

	/**
	 * Registers type
	 *
	 * @since 0.3.1
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_input_type(
			'TECDateRangeInput',
			[
				'description' => __( 'Date range object', 'ql-events' ),
				'fields'      => [
					'start' => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'Range start datetime.', 'ql-events' ),
					],
					'end'   => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'Range end datetime.', 'ql-events' ),
					],
				],
			]
		);
	}
}
