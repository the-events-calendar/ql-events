<?php
/**
 * WPInterface Type - Order
 *
 * Registers Order interface.
 *
 * @package WPGraphQL\QL_Events\Type\WPInterface;
 * @since   0.2.0
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
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public static function register_interface() {
		register_graphql_interface_type(
			'TECOrder',
			[
				'interfaces'  => [ 'Node' ],
				'description' => __( 'Order object', 'ql-events' ),
				'fields'      => self::get_fields(),
				'resolveType' => function ( $value ) {
					$type_registry = \WPGraphQL::get_type_registry();
					switch ( $value->post_type ) {
						case tribe( 'tickets.commerce.paypal' )->attendee_object:
							return $type_registry->get_type( 'PayPalOrder' );

						default:
							/**
							 * Filter the TECOrder resolve type.
							 *
							 * @param string|null  $type_name  Name of type to be resolved.
							 * @param mixed        $value      Data source.
							 * @since 0.3.0
							 */
							$type = apply_filters( 'ql_events_resolve_tec_order_type', null, $value );
							if ( ! empty( $type ) ) {
								return $type;
							}

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
	 * @since 0.2.0
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
