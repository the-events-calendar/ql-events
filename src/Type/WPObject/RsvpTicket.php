<?php
/**
 * GraphQL Object Type - RsvpTicket
 *
 * @see https://docs.theRsvpTicketscalendar.com/reference/functions/tribe_get_RsvpTicket/
 *
 * @package WPGraphQL\TEC\Type\Object
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Type\WPObject;

use WPGraphQL\TEC\Type\Enum\CurrencyPositionEnum;

/**
 * Class - RsvpTicket
 */
class RsvpTicket {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'RsvpTicket';

	/**
	 * The type used by WordPress.
	 *
	 * @var string
	 */
	public static $wp_type = 'tribe_rsvp_tickets';

	/**
	 * {@inheritDoc}
	 */
	public static function register_fields() : void {
		self::register_core_fields();
	}

	/**
	 * Register the fields used by TEC Core plugin.
	 */
	public static function register_core_fields() : void {
		register_graphql_fields(
			self::$type,
			[
				'attendeesGoing'    => [
					'type'        => 'Int',
					'description' => __( 'The number of attendees who have RSVPed \"Going\" for this ticket.', 'wp-graphql-tec' ),
				],
				'attendeesNotGoing' => [
					'type'        => 'Int',
					'description' => __( 'The number of attendees who have RSVPed \"Not Going\" for this ticket.', 'wp-graphql-tec' ),
				],
			]
		);
	}
}
