<?php
/**
 * WPObject Type - PayPalAttendee
 *
 * Registers "PayPalAttendee" WPObject type fields
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Class - PayPalAttendee_Type
 */
class PayPalAttendee_Type {

	use Attendee;

	/**
	 * Stores ticket manager
	 *
	 * @since 0.0.1
	 *
	 * @var Tribe__Tickets__Commerce__PayPal__Main
	 */
	private static $manager;

	/**
	 * Returns ticket manager.
	 *
	 * @since 0.0.1
	 *
	 * @return Tribe__Tickets__Commerce__PayPal__Main
	 */
	private static function manager() {
		if ( is_null( self::$manager ) ) {
			self::$manager = tribe( 'tickets.commerce.paypal' );
		}

		return self::$manager;
	}

	/**
	 * Registers "Attendee" type fields.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register_fields() {
		deregister_graphql_field( 'PayPalAttendee', 'ticket' );
		deregister_graphql_field( 'PayPalAttendee', 'order' );
		deregister_graphql_field( 'PayPalAttendee', 'fullName' );
		deregister_graphql_field( 'PayPalAttendee', 'email' );
		deregister_graphql_field( 'PayPalAttendee', 'event' );
		deregister_graphql_field( 'PayPalAttendee', 'checkedIn' );
		deregister_graphql_field( 'PayPalAttendee', 'securityCode' );
		deregister_graphql_field( 'PayPalAttendee', 'paidPrice' );
		deregister_graphql_field( 'PayPalAttendee', 'priceCurrencySymbol' );

		// Register common fields.
		register_graphql_fields(
			'PayPalAttendee',
			self::fields()
		);

		register_graphql_fields(
			'PayPalAttendee',
			[
				'ticket'             => [
					'type'        => 'Ticket',
					'description' => __( 'The ticket the attendee has purchased.', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$ticket_id = get_post_meta( $source->ID, self::manager()::ATTENDEE_PRODUCT_KEY, true );
						return ! empty( $ticket_id )
							? DataSource::resolve_post_object( $ticket_id, $context )
							: null;
					},
				],
				'order'              => [
					'type'        => 'TECOrder',
					'description' => __( 'Attendee\'s ticket order.', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$order_id = get_post_meta( $source->ID, self::manager()::ATTENDEE_ORDER_KEY, true );
						return ! empty( $order_id )
							? DataSource::resolve_post_object( $order_id, $context )
							: null;
					},
				],
				'fullName'           => [
					'type'        => 'String',
					'description' => __( 'Full name of the tickets PayPal "buyer"', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$full_name = get_post_meta( $source->ID, self::manager()->full_name, true );
						return ! empty( $full_name ) ? $full_name : null;
					},
				],
				'email'              => [
					'type'        => 'String',
					'description' => __( 'email of the tickets PayPal "buyer"', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$email = get_post_meta( $source->ID, self::manager()->email, true );
						return ! empty( $email ) ? $email : null;
					},
				],
				'ticketName'         => [
					'type'        => 'String',
					'description' => __( 'Name of purchased ticket', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$name = get_post_meta( $source->ID, self::manager()->deleted_product, true );
						return ! empty( $name ) ? $name : null;
					},
				],
				'attendeeStatus'     => [
					'type'        => 'String',
					'description' => __( 'Attendee\'s PayPal status', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$status = get_post_meta( $source->ID, self::manager()->attendee_tpp_key, true );
						return ! empty( $status ) ? $status : null;
					},
				],
				'showOnAttendeeList' => [
					'type'        => 'Boolean',
					'description' => __( 'Whether attendee should appear on the attendee list.', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$show = get_post_meta( $source->ID, self::manager()->attendee_optout_key, true );
						return ! empty( $show ) ? $show : null;
					},
				],
			]
		);
	}
}
