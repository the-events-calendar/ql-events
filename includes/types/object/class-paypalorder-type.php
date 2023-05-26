<?php
/**
 * WPObject Type - PayPalOrder
 *
 * Registers "PayPalOrder" WPObject type fields
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Class - Attendee_Type
 */
class PayPalOrder_Type {
	/**
	 * Registers "Attendee" type fields.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register_fields() {
		deregister_graphql_field( 'PayPalOrder', 'databaseId' );
		register_graphql_fields(
			'PayPalOrder',
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
