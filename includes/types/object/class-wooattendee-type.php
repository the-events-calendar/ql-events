<?php
/**
 * WPObject Type - WooAttendee
 *
 * Registers "WooAttendee" WPObject type fields
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\WooCommerce\Data\Factory;

/**
 * Class - WooAttendee_Type
 */
class WooAttendee_Type {

	use Attendee;

	/**
	 * Stores ticket manager
	 *
	 * @since 0.0.1
	 *
	 * @var Tribe__Tickets_Plus__Commerce__WooCommerce__Main
	 */
	private static $manager;

	/**
	 * Returns ticket manager.
	 *
	 * @since 0.0.1
	 *
	 * @return Tribe__Tickets_Plus__Commerce__WooCommerce__Main
	 */
	private static function manager() {
		if ( is_null( self::$manager ) ) {
			self::$manager = tribe( 'tickets-plus.commerce.woo' );
		}

		return self::$manager;
	}

	/**
	 * Resolves the GraphQL type for "WooAttendee".
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public static function register_to_attendee_interface() {
		add_filter(
			'ql_events_resolve_attendee_type',
			[ static::class, 'resolve_woo_attendee' ],
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
	 * @param mixed $value  Attendee data object.
	 *
	 * @return mixed
	 */
	public static function resolve_woo_attendee( $type, $value ) {
		$type_registry = \WPGraphQL::get_type_registry();
		if ( tribe( 'tickets-plus.commerce.woo' )->attendee_object === $post_type ) {
			$type = $type_registry->get_type( 'WooAttendee' );
		}

		return $type;
	}

	/**
	 * Registers "Attendee" type fields.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register_fields() {
		deregister_graphql_field( 'WooAttendee', 'ticket' );
		deregister_graphql_field( 'WooAttendee', 'order' );
		deregister_graphql_field( 'WooAttendee', 'fullName' );
		deregister_graphql_field( 'WooAttendee', 'email' );
		deregister_graphql_field( 'WooAttendee', 'event' );
		deregister_graphql_field( 'WooAttendee', 'checkedIn' );
		deregister_graphql_field( 'WooAttendee', 'securityCode' );
		deregister_graphql_field( 'WooAttendee', 'paidPrice' );
		deregister_graphql_field( 'WooAttendee', 'priceCurrencySymbol' );

		// Register common fields.
		register_graphql_fields(
			'WooAttendee',
			self::fields()
		);

		register_graphql_fields(
			'WooAttendee',
			[
				'ticket'    => [
					'type'        => 'Ticket',
					'description' => __( 'The ticket the attendee has purchased.', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$ticket_id = get_post_meta( $source->ID, self::manager()::ATTENDEE_PRODUCT_KEY, true );
						return ! empty( $ticket_id )
							? Factory::resolve_crud_object( $ticket_id, $context )
							: null;
					},
				],
				'order'     => [
					'type'        => 'TECOrder',
					'description' => __( 'Attendee\'s ticket order.', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$order_id = get_post_meta( $source->ID, self::manager()::ATTENDEE_ORDER_KEY, true );
						return ! empty( $order_id )
							? Factory::resolve_crud_object( $order_id, $context )
							: null;
					},
				],
				'orderItem' => [
					'type'        => 'LineItem',
					'description' => __( 'Order item for Attendee\'s ticket.', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$line_item_key = get_post_meta( $source->ID, self::manager()::ATTENDEE_ORDER_ITEM_KEY, true );
						return ! empty( $line_item_key )
							? \WC()->order_factory::get_order_item( $line_item_key )
							: null;
					},
				],
				'fullName'  => [
					'type'        => 'String',
					'description' => __( 'Full name of the tickets PayPal "buyer"', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$full_name = get_post_meta( $source->ID, self::manager()->full_name, true );
						return ! empty( $full_name ) ? $full_name : null;
					},
				],
				'email'     => [
					'type'        => 'String',
					'description' => __( 'email of the tickets PayPal "buyer"', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$email = get_post_meta( $source->ID, self::manager()->email, true );
						return ! empty( $email ) ? $email : null;
					},
				],
				'data'      => [
					'type'        => [ 'list_of' => 'MetaData' ],
					'description' => __( 'Attendee\'s Data', 'ql-events' ),
					'args'        => [
						'key'      => [
							'type'        => 'String',
							'description' => __( 'Retrieve meta by key', 'ql-events' ),
						],
						'keysIn'   => [
							'type'        => [ 'list_of' => 'String' ],
							'description' => __( 'Retrieve multiple metas by key', 'ql-events' ),
						],
						'multiple' => [
							'type'        => 'Boolean',
							'description' => __( 'Retrieve meta with matching keys', 'ql-events' ),
						],
					],
					'resolve'     => function( $source ) {
						$decorator          = tribe( Attendee::class );
						$decorated_attendee = $decorator->get_attendee( get_post( $source->ID ) );

						$meta               = tribe( 'tickets-plus.meta' );
						$attendee_meta_data = $meta->get_attendee_meta_fields( $decorated_attendee->ticket_id, $decorated_attendee->ID );
						if ( isset( $attendee_meta_data[0] ) ) {
							unset( $attendee_meta_data[0] );
						}

						if ( ! is_array( $attendee_meta_data ) ) {
							return [];
						}

						return array_map(
							function( $key, $value ) {
								return (object) compact( 'value', 'key' );
							},
							array_keys( $attendee_meta_data ),
							array_values( $attendee_meta_data )
						);
					},
				],
			]
		);
	}
}
