<?php
/**
 * WPInputObjectType - MetaDataInput
 *
 * @package WPGraphQL\WooCommerce\Type\WPInputObject
 * @since   0.2.0
 */

namespace WPGraphQL\QL_Events\Type\WPInputObject;

/**
 * Class Meta_Data_Input
 */
class Meta_Data_Input {

	/**
	 * Registers type
	 */
	public static function register() {
		// Bail early if WooGraphQL installed and active.
		if ( class_exists( 'WP_GraphQL_WooCommerce' ) ) {
			return;
		}

		register_graphql_input_type(
			'MetaDataInput',
			array(
				'description' => __( 'Meta data.', 'wp-graphql-woocommerce' ),
				'fields'      => array(
					'id'    => array(
						'type'        => 'String',
						'description' => __( 'Meta ID.', 'wp-graphql-woocommerce' ),
					),
					'key'   => array(
						'type'        => array( 'non_null' => 'String' ),
						'description' => __( 'Meta key.', 'wp-graphql-woocommerce' ),
					),
					'value' => array(
						'type'        => array( 'non_null' => 'String' ),
						'description' => __( 'Meta value.', 'wp-graphql-woocommerce' ),
					),
				),
			)
		);
	}
}
