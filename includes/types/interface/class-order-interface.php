<?php
/**
 * WPInterface Type - Order
 *
 * Registers Order interface.
 *
 * @package WPGraphQL\QL_Events\Type\WPInterface;
 */

namespace WPGraphQL\QL_Events\Type\WPInterface;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\WooCommerce\Data\Factory;
use WP_GraphQL_WooCommerce;

/**
 * Class - Order_Interface
 */
class Order_Interface {
	/**
	 * Registers the "Order" interface.
	 *
	 * @param \WPGraphQL\Registry\TypeRegistry $type_registry  Instance of the WPGraphQL TypeRegistry.
	 */
	public static function register_interface( &$type_registry ) {
		register_graphql_interface_type(
			'TECOrder',
			[
				'description' => __( 'Order object', 'ql-events' ),
				'fields'      => self::get_fields(),
				'resolveType' => function ( $value ) use ( &$type_registry ) {
					switch ( $value->post_type ) {
						case tribe( 'tickets.commerce.paypal' )->attendee_object:
							return $type_registry->get_type( 'PayPalOrder' );
						case 'shop_order':
							return $type_registry->get_type( 'Order' );
						default:
							throw new UserError(
								sprintf(
									/* translators: %s: Product type */
									__( 'The "%s" ticket type is not supported by the core QL-Events schema.', 'ql-events' ),
									$value->post_type
								)
							);
					}
				},
			]
		);
	}

	/**
	 * Defines Ticket fields. All child type must have these fields as well.
	 *
	 * @return array
	 */
	public static function get_fields() {
		return [
			'id'         => [
				'type'        => [ 'non_null' => 'ID' ],
				'description' => __( 'Order Unique ID.', 'ql-events' ),
				'resolve'     => function( $source ) {
					return $source->id;
				},
			],
			'databaseId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'Order database ID', 'ql-events' ),
				'resolve'     => function( $source ) {
					return $source->ID;
				},
			],
		];
	}
}
