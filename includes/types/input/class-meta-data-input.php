<?php
/**
 * WPInputObjectType - MetaDataInput
 *
 * @package WPGraphQL\WooCommerce\Type\WPInputObject
 * @since   0.1.0
 */

namespace WPGraphQL\QL_Events\Type\WPInputObject;

use WPGraphQL\QL_Events\QL_Events;

/**
 * Class Meta_Data_Input
 */
class Meta_Data_Input {

	/**
	 * Registers type
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_input_type(
			'MetaDataInput',
			[
				'description' => __( 'Meta data.', 'ql-events' ),
				'fields'      => [
					'id'    => [
						'type'        => 'String',
						'description' => __( 'Meta ID.', 'ql-events' ),
					],
					'key'   => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'Meta key.', 'ql-events' ),
					],
					'value' => [
						'type'        => [ 'non_null' => 'String' ],
						'description' => __( 'Meta value.', 'ql-events' ),
					],
				],
			]
		);
	}
}
