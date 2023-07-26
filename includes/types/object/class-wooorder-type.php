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
use WPGraphQL\Model\Post;

/**
 * Class - WooOrder_Type
 */
class WooOrder_Type {
	/**
	 * Resolves the GraphQL type for "WooOrder".
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public static function register_to_order_interface() {
		add_filter(
			'ql_events_resolve_tec_order_type',
			[ __CLASS__, 'resolve_woo_order' ],
			10,
			2
		);
	}

	/**
	 * Callback for resolver
	 *
	 * @since 0.3.0
	 *
	 * @param mixed $type   GraphQL Type.
	 * @param mixed $value  Order data object.
	 *
	 * @return mixed
	 */
	public static function resolve_woo_order( $type, $value ) {
		$type_registry = \WPGraphQL::get_type_registry();
		if ( 'shop_order' === $value->post_type ) {
			$type = $type_registry->get_type( 'Order' );
		}

		return $type;
	}

	/**
	 * Registers "Order" type fields.
	 *
	 * @since 0.0.1
	 *
	 * @return void
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
				'attendees'  => [
					'type'        => [ 'list_of' => 'Attendee' ],
					'description' => __( 'Attendees connected to order', 'ql-events' ),
					'resolve'     => function( $source ) {
						$woo_provider = tribe( 'tickets-plus.commerce.woo' );
						$has_tickets  = $source->get_meta( $woo_provider->order_has_tickets );

						if ( ! $has_tickets ) {
							return [];
						}

						$attendee_list = $woo_provider->get_attendees_by_id( $source->get_id() );
						$attendees     = [];
						foreach ( $attendee_list as $attendee ) {
							$attendees[] = new Post( get_post( $attendee['attendee_id'] ) );
						}

						return $attendees;
					},
				],
			]
		);
	}
}
