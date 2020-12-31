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
	 * @var Tribe__Tickets__RSVP
	 */
	private static $manager;

	/**
	 * Returns ticket manager.
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
	 * Registers "RSVPAttendee" type fields.
	 */
	public static function register_fields() {
		register_graphql_fields(
			'RSVPAttendee',
			array_merge(
				self::fields(),
				array(
					'ticket'     => array(
						'type'        => 'RSVPTicket',
						'description' => __( 'The ticket the attendee has purchased.', 'ql-events' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							$ticket_id = get_post_meta( $source->ID, self::manager()::ATTENDEE_PRODUCT_KEY, true );
							return ! empty( $ticket_id )
								? DataSource::resolve_post_object( $ticket_id, $context )
								: null;
						},
					),
					'rsvpStatus' => array(
						'type'        => 'Boolean',
						'description' => __( 'Does Attendee have RSVP status.', 'ql-events' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							$rsvp_status = get_post_meta( $source->ID, self::manager()::ATTENDEE_RSVP_KEY, true );
							return ! empty( $rsvp_status ) ? $rsvp_status : null;
						},
					),
					'fullName'   => array(
						'type'        => 'String',
						'description' => __( 'Full name of the tickets PayPal "buyer"', 'ql-events' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							$full_name = get_post_meta( $source->ID, self::manager()->full_name, true );
							return ! empty( $full_name ) ? $full_name : null;
						},
					),
					'email'      => array(
						'type'        => 'String',
						'description' => __( 'email of the tickets PayPal "buyer"', 'ql-events' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							$email = get_post_meta( $source->ID, self::manager()->email, true );
							return ! empty( $email ) ? $email : null;
						},
					),
					'ticketName' => array(
						'type'        => 'String',
						'description' => __( 'Name of purchased ticket', 'ql-events' ),
						'resolve'     => function( $source, array $args, AppContext $context ) {
							$name = get_post_meta( $source->ID, self::manager()->deleted_product, true );
							return ! empty( $name ) ? $name : null;
						},
					),
				)
			)
		);
	}
}
