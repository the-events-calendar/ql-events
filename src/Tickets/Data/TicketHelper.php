<?php
/**
 * Ticket Helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Tickets\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data;

use GraphQL\Deferred;
use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;
use WP_Post;
use WPGraphQL\TEC\Abstracts\DataHelper;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketTypeEnum;
use WPGraphQL\TEC\Tickets\Type\Input\IntRangeInput;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Ticket Helper
 */
class TicketHelper extends DataHelper {
	/**
	 * The helper name. E.g. `events` or `tickets`.
	 *
	 * @var string
	 */
	public static string $name = 'tickets';

	/**
	 * The GraphQL type. E.g. `Event` or `RsvpTicket`.
	 *
	 * @var string
	 */
	public static string $type = 'Ticket';

	/**
	 * The WordPress type. E.g. `tribe_events` or `tec_tc_ticket`.
	 *
	 * @var string
	 */
	public static string $wp_type = 'Ticket';

	/**
	 * The name of the DataLoader to use.
	 *
	 * @var string
	 */
	public static string $loader_name = 'ticket';

	/**
	 * {@inheritDoc}
	 */
	public static function resolver() : string {
		return __NAMESPACE__ . '\\Connection\\TicketConnectionResolver';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function connection_args() : array {
		return [
			'attendeesBetween' => [
				'type'        => IntRangeInput::$type,
				'description' => __( 'Filter by tickets that have a number of attendees between two values', 'wp-graphql-tec' ),
			],
			'attendeesMax'     => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a maximum number of attendees', 'wp-graphql-tec' ),
			],
			'attendeesMin'     => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a minimum number of attendees', 'wp-graphql-tec' ),
			],
			'availableFrom'    => [
				'type'        => 'String',
				'description' => __( 'Filter tickets by their availability start date. Accepts a UTC date or timestamp', 'wp-graphql-tec' ),
			],
			'availableUntil'   => [
				'type'        => 'String',
				'description' => __( 'Filter tickets by their availability end date. Accepts a UTC date or timestamp', 'wp-graphql-tec' ),
			],
			'capacityBetween'  => [
				'type'        => IntRangeInput::$type,
				'description' => __( 'Filter by tickets that have a capacity between two values', 'wp-graphql-tec' ),
			],
			'capacityMax'      => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a maximum capacity', 'wp-graphql-tec' ),
			],
			'capacityMin'      => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a minimum capacity', 'wp-graphql-tec' ),
			],
			'checkedInBetween' => [
				'type'        => IntRangeInput::$type,
				'description' => __( 'Filter by tickets that have a number of checked-in attendees between two values', 'wp-graphql-tec' ),
			],
			'checkedInMax'     => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a maximum number of checked-in attendees', 'wp-graphql-tec' ),
			],
			'checkedInMin'     => [
				'type'        => 'Int',
				'description' => __( 'Filter by tickets that have a minimum number of checked-in attendees', 'wp-graphql-tec' ),
			],
			'currencyCode'     => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filter tickets by their provider currency codes. Accepts a 3-letter currency_codes, or an array of codes.', 'wp-graphql-tec' ),
			],
			'eventIn'          => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters tickets attached to a specific post ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'eventNotIn'       => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters tickets not attached to a specific post ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'eventStatus'      => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Filters tickets by their associated post`s status or array of statii.', 'wp-graphql-tec' ),
			],
			'hasAttendeeMeta'  => [
				'type'        => 'Boolean',
				'description' => __( 'Filters tickets depending on them having additional information available and active or not based on the provided value.', 'wp-graphql-tec' ),
			],
			'isActive'         => [
				'type'        => 'Boolean',
				'description' => __( 'Filters tickets by if they are currently available or available in the future based on the provided value', 'wp-graphql-tec' ),
			],
			'isAvailable'      => [
				'type'        => 'Boolean',
				'description' => __( 'Filters tickets by if they are currently available or available in the future based on the provided value.', 'wp-graphql-tec' ),
			],
			'provider'         => [
				'type'        => [ 'list_of' => TicketTypeEnum::$type ],
				'description' => __( 'Filters tickets by if they are currently available or available in the future based on the provided value.', 'wp-graphql-tec' ),
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

}
