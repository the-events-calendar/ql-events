<?php
/**
 * Attendee Helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Tickets\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data;

use Tribe__Tickets__Tickets;
use WPGraphQL\TEC\Abstracts\DataHelper;
use WPGraphQL\TEC\Tickets\Type\Enum\AttendeeOptoutStatusEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketTypeEnum;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Ticket Helper
 */
class AttendeeHelper extends DataHelper {
	/**
	 * The helper name. E.g. `events` or `tickets`.
	 *
	 * @var string
	 */
	public static string $name = 'attendees';

	/**
	 * The GraphQL type. E.g. `Event` or `RsvpTicket`.
	 *
	 * @var string
	 */
	public static string $type = 'Attendee';

	/**
	 * The WordPress type. E.g. `tribe_events` or `tec_tc_ticket`.
	 *
	 * @var string
	 */
	public static string $wp_type = 'Attendee';

	/**
	 * The name of the DataLoader to use.
	 *
	 * @var string
	 */
	public static string $loader_name = 'attendee';

	/**
	 * {@inheritDoc}
	 */
	public static function resolver() : string {
		return __NAMESPACE__ . '\\Connection\\AttendeeConnectionResolver';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function connection_args() : array {
		return [
			'eventIdIn'              => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees attached to a specific post ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'eventIdNotIn'           => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees not attached to a specific post ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'eventStatusIn'          => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees by their associated post`s status or array of statii.', 'wp-graphql-tec' ),
			],
			'hasAttendeeMeta'        => [
				'type'        => 'Boolean',
				'description' => __( 'Filters attendees depending on them having additional information available and active or not based on the provided value.', 'wp-graphql-tec' ),
			],
			'holderEmail'            => [
				'type'        => 'String',
				'description' => __( 'Filters attendees that match the specified ticket holder email address.', 'wp-graphql-tec' ),
			],
			'holderEmailIn'          => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that match one of a list specified ticket holder email address.', 'wp-graphql-tec' ),
			],
			'holderEmailNotIn'       => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that do NOT match one of a list specified ticket holder email address.', 'wp-graphql-tec' ),
			],
			'holderName'             => [
				'type'        => 'String',
				'description' => __( 'Filters attendees that match the specified ticket holder name.', 'wp-graphql-tec' ),
			],
			'holderNameIn'           => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that match one of a list specified ticket holder name.', 'wp-graphql-tec' ),
			],
			'holderNameNotIn'        => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that do NOT match one of a list specified ticket holder name.', 'wp-graphql-tec' ),
			],
			'isShowAttendeesEnabled' => [
				'type'        => 'Boolean',
				'description' => __( 'Filters attendee to those with "Show attendees list on event page" set to true.', 'wp-graphql-tec' ),
			],
			'isCheckedIn'            => [
				'type'        => 'Boolean',
				'description' => __( 'Filters attendees depending on their checkedin status.', 'wp-graphql-tec' ),
			],
			'optoutStatus'           => [
				'type'        => AttendeeOptoutStatusEnum::$type,
				'description' => __( 'Filters attenders depending on if they have opted out of being shown publicly.', 'wp-graphql-tec' ),
			],
			'orderIdIn'              => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees attached to a specific order ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'orderIdNotIn'           => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees not attached to a specific order ID or array of IDs.', 'wp-graphql-tec' ),
			],
			// @todo convert to enum.
			'orderStatusIn'          => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that match one of a list specified order statii.', 'wp-graphql-tec' ),
			],
			// @todo convert to enum.
			'orderStatusNotIn'       => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that do NOT match one of a list specified order statii.', 'wp-graphql-tec' ),
			],
			'price'                  => [
				'type'        => 'Int',
				'description' => __( 'Filters attendees who paid the provided price.', 'wp-graphql-tec' ),
			],
			'priceMin'               => [
				'type'        => 'Int',
				'description' => __( 'Filters attendees by a minimum paid price.', 'wp-graphql-tec' ),
			],
			'priceMax'               => [
				'type'        => 'Int',
				'description' => __( 'Filters attendees by a maximum paid price.', 'wp-graphql-tec' ),
			],
			'productIdIn'            => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees attached to the provided list of product databseIds.', 'wp-graphql-tec' ),
			],
			'productIdNotIn'         => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees that are NOT attached to a list of provided product databseIds.', 'wp-graphql-tec' ),
			],
			'providerIn'             => [
				'type'        => [ 'list_of' => TicketTypeEnum::$type ],
				'description' => __( 'Filters attendees by those that match a list of ET ticket providers.', 'wp-graphql-tec' ),
			],
			'providerNotIn'          => [
				'type'        => [ 'list_of' => TicketTypeEnum::$type ],
				'description' => __( 'Filters attendees by those that do NOT match a list of ET ticket providers.', 'wp-graphql-tec' ),
			],
			'purchaserEmail'         => [
				'type'        => 'String',
				'description' => __( 'Filters attendees that match the specified purchaser email address.', 'wp-graphql-tec' ),
			],
			'purchaserEmailIn'       => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that match one of a list of purchaser email address.', 'wp-graphql-tec' ),
			],
			'purchaserEmailNotIn'    => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that do NOT match one of a list of purchaser email address.', 'wp-graphql-tec' ),
			],
			'purchaserName'          => [
				'type'        => 'String',
				'description' => __( 'Filters attendees that match the specified purchaser name.', 'wp-graphql-tec' ),
			],
			'purchaserNameIn'        => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that match one of a list of purchaser names.', 'wp-graphql-tec' ),
			],
			'purchaserNameNotIn'     => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees that do NOT match one of a list of purchaser names.', 'wp-graphql-tec' ),
			],
			'rsvpStatus'             => [
				'type'        => 'String',
				'description' => __( 'Filters attendees by a specific RSVP status.', 'wp-graphql-tec' ),
			],
			'rsvpStatusOrNone'       => [
				'type'        => 'String',
				'description' => __( 'Filters attendees by a specific RSVP status or that have no status at all.', 'wp-graphql-tec' ),
			],
			'securityCodeIn'         => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees by those that match the provided list of security codes.', 'wp-graphql-tec' ),
			],
			'securityCodeNotIn'      => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters attendees by those that do NOT match the provided list of security codes', 'wp-graphql-tec' ),
			],
			'ticketIdIn'             => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees by those that match the provided list of ticket databaseIds.', 'wp-graphql-tec' ),
			],
			'ticketIdNotIn'          => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees by those that do NOT match the provided list of ticket databaseIds', 'wp-graphql-tec' ),
			],
			'userIdIn'               => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees by those that match the provided list of WordPress User databaseIds.', 'wp-graphql-tec' ),
			],
			'userIdNotIn'            => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters attendees by those that do NOT match the provided list WordPress User databaseIds', 'wp-graphql-tec' ),
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function process_where_args( array $args ) : array {
		if ( isset( $args['provider'] ) ) {
			$provider = $args['provider'];
			$provider = is_array( $provider ) ? $provider : [ $provider ];
			$provider = array_map( [ Utils::class, 'get_et_provider_for_type' ], $provider );
		}

		return $args;
	}

	/**
	 * Grabs the attendee databaseIds for the source, if they're not set by the model.
	 *
	 * Necessary when the source uses a built-in model.
	 *
	 * @param mixed $source .
	 */
	public static function get_attendee_ids( $source ) : ?array {
		if ( ! empty( $source->attendeeDatabaseIds ) ) {
			return $source->attendeeDatabaseIds;
		}

		$attendees = tribe_tickets_get_attendees( $source->ID );
		if ( empty( $attendees ) ) {
			return null;
		}

		$attendee_ids = wp_list_pluck( $attendees, 'attendee_id' );

		return $attendee_ids ?: null;
	}
}
