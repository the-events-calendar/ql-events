<?php
/**
 * WPObject Type - Order
 *
 * Reregisters some "Order" WPObject type fields
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Class - WooOrder_Type
 */
class WooOrder_Type {
	/**
	 * Registers "Order" type fields.
	 */
	public static function register_fields() {
		deregister_graphql_field( 'Order', 'databaseId' );
		register_graphql_fields(
			'Order',
			[
				'databaseId' => [
					'type'        => [ 'non_null' => 'Int' ],
					'description' => __( 'Order database ID', 'ql-events' ),
					'resolve'     => function( $source ) {
						return $source->ID;
					},
				],
			]
		);
	}
}
