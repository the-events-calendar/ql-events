<?php
/**
 * GraphQL Object Type - Event
 *
 * @see https://docs.theeventscalendar.com/reference/functions/tribe_get_event/
 *
 * @package WPGraphQL\TEC\Events\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\WPObject;

use WPGraphQL\TEC\Events\Type\Enum\CurrencyPositionEnum;

/**
 * Class - Event
 */
class Event {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'Event';

	/**
	 * The type used by WordPress.
	 *
	 * @var string
	 */
	public static $wp_type = 'tribe_events';

	/**
	 * {@inheritDoc}
	 */
	public static function register_fields() : void {
		register_graphql_fields(
			self::$type,
			[
				'cost'             => [
					'type'        => 'String',
					'description' => __( 'The event formatted cost string, as returned by the `tribe_get_cost` function.', 'wp-graphql-tec' ),
				],
				'costMax'          => [
					'type'        => 'String',
					'description' => __( 'The maximum cost for the event.', 'wp-graphql-tec' ),
				],
				'costMin'          => [
					'type'        => 'String',
					'description' => __( 'The minimum cost for the event.', 'wp-graphql-tec' ),
				],
				'currencyPosition' => [
					'type'        => CurrencyPositionEnum::$type,
					'description' => __( 'Where to display the currency symbol in relation to the event cost.', 'wp-graphql-tec' ),
				],
				'currencySymbol'   => [
					'type'        => 'String',
					'description' => __( 'The currency symbol used when displaying the event c0st.', 'wp-graphql-tec' ),
				],
				'duration'         => [
					'type'        => 'Int',
					'description' => __( 'The event duration in seconds.', 'wp-graphql-tec' ),
				],
				'endDate'          => [
					'type'        => 'String',
					'description' => __( 'The event end date, in Y-m-d H:i:s format.', 'wp-graphql-tec' ),
				],
				'endDateUTC'       => [
					'type'        => 'String',
					'description' => __( 'The event UTC end date, in Y-m-d H:i:s format.', 'wp-graphql-tec' ),
				],
				'eventUrl'         => [
					'type'        => 'String',
					'description' => __( 'The external URL for the event.', 'wp-graphql-tec' ),
				],
				'hideFromUpcoming' => [
					'type'        => 'Boolean',
					'description' => __( 'Whether the event is hidden from the event list.', 'wp-graphql-tec' ),
				],
				'isAllDay'         => [
					'type'        => 'Boolean',
					'description' => __( 'Whether the event is an all-day one or not.', 'wp-graphql-tec' ),
				],
				'isFeatured'       => [
					'type'        => 'Boolean',
					'description' => __( 'Whether the event is a featured one or not.', 'wp-graphql-tec' ),
				],
				'isMultiday'       => [
					'type'        => 'Boolean',
					'description' => __( 'Whether the event is multi-day or not.', 'wp-graphql-tec' ),
				],
				'isPast'           => [
					'type'        => 'Boolean',
					'description' => __( 'Whether the event date has passed.', 'wp-graphql-tec' ),
				],
				'isSticky'         => [
					'type'        => 'Boolean',
					'description' => __( 'Whether the event is sticky in Month view.', 'wp-graphql-tec' ),
				],
				'origin'           => [
					'type'        => 'String',
					'description' => __( 'The event origin.', 'wp-graphql-tec' ),
				],
				'showMap'          => [
					'type'        => 'Boolean',
					'description' => __( 'Whether to display the event map.', 'wp-graphql-tec' ),
				],
				'showMapLink'      => [
					'type'        => 'Boolean',
					'description' => __( 'Whether to display a link to the Map.', 'wp-graphql-tec' ),
				],
				'startDate'        => [
					'type'        => 'String',
					'description' => __( 'The event start date, in Y-m-d H:i:s format.', 'wp-graphql-tec' ),
				],
				'startDateUTC'     => [
					'type'        => 'String',
					'description' => __( 'The event UTC start date, in Y-m-d H:i:s format.', 'wp-graphql-tec' ),
				],
				'timezone'         => [
					'type'        => 'String',
					'description' => __( 'The event timezone string.', 'wp-graphql-tec' ),
				],
				'timezoneAbbr'     => [
					'type'        => 'String',
					'description' => __( 'Event timezone abbreviation.', 'wp-graphql-tec' ),
				],
			]
		);
	}
}
