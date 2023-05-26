<?php
/**
 * WPObject Type - RSVPAttendee
 *
 * Registers "RSVPAttendee" WPObject type fields
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;

/**
 * Class - RSVPAttendee_Type
 */
class RSVPAttendee_Type {

	use Attendee;

	/**
	 * Stores ticket manager
	 *
	 * @since 0.0.1
	 *
	 * @var Tribe__Tickets__RSVP
	 */
	private static $manager;

	/**
	 * Returns ticket manager.
	 *
	 * @since 0.0.1
	 *
	 * @return Tribe__Tickets__RSVP
	 */
	private static function manager() {
		if ( is_null( self::$manager ) ) {
			self::$manager = tribe( 'tickets.rsvp' );
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
		deregister_graphql_field( 'RSVPAttendee', 'ticket' );
		deregister_graphql_field( 'RSVPAttendee', 'fullName' );
		deregister_graphql_field( 'RSVPAttendee', 'email' );
		deregister_graphql_field( 'RSVPAttendee', 'event' );
		deregister_graphql_field( 'RSVPAttendee', 'checkedIn' );
		deregister_graphql_field( 'RSVPAttendee', 'securityCode' );
		deregister_graphql_field( 'RSVPAttendee', 'paidPrice' );
		deregister_graphql_field( 'RSVPAttendee', 'priceCurrencySymbol' );

		// Register common fields.
		register_graphql_fields(
			'RSVPAttendee',
			self::fields()
		);

		register_graphql_fields(
			'RSVPAttendee',
			[
				'ticket'     => [
					'type'        => 'Ticket',
					'description' => __( 'The ticket the attendee has purchased.', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$ticket_id = get_post_meta( $source->ID, self::manager()::ATTENDEE_PRODUCT_KEY, true );
						return ! empty( $ticket_id )
							? DataSource::resolve_post_object( $ticket_id, $context )
							: null;
					},
				],
				'rsvpStatus' => [
					'type'        => 'Boolean',
					'description' => __( 'Does Attendee have RSVP status.', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$rsvp_status = get_post_meta( $source->ID, self::manager()::ATTENDEE_RSVP_KEY, true );
						return ! empty( $rsvp_status ) ? $rsvp_status : null;
					},
				],
				'fullName'   => [
					'type'        => 'String',
					'description' => __( 'Full name of the tickets PayPal "buyer"', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$full_name = get_post_meta( $source->ID, self::manager()->full_name, true );
						return ! empty( $full_name ) ? $full_name : null;
					},
				],
				'email'      => [
					'type'        => 'String',
					'description' => __( 'email of the tickets PayPal "buyer"', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$email = get_post_meta( $source->ID, self::manager()->email, true );
						return ! empty( $email ) ? $email : null;
					},
				],
				'ticketName' => [
					'type'        => 'String',
					'description' => __( 'Name of purchased ticket', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$name = get_post_meta( $source->ID, self::manager()->deleted_product, true );
						return ! empty( $name ) ? $name : null;
					},
				],
			]
		);
	}
}
