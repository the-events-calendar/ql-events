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
				'attendees' => [
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
					}
				]
			]
		);
	}
}
