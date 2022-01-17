<?php
/**
 * Extends the EventHelper.
 *
 * @package WPGraphQL\TEC\EventsPro\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\EventsPro\Data;

use WPGraphQL\TEC\EventsPro\Type\Input\CustomFieldFilterInput;
use WPGraphQL\TEC\EventsPro\Type\Input\CustomFieldRangeFilterInput;
use WPGraphQL\TEC\EventsPro\Type\Input\GeolocationFilterInput;
use WPGraphQL\TEC\EventsPro\Type\Input\NearFilterInput;

/**
 * Class - Event Helper
 */
class EventHelper {

	/**
	 * Extends event connection with where args.
	 *
	 * @param array $connection_args .
	 */
	public static function add_where_args_to_events_connection( array $connection_args ) : array {
		return array_merge(
			$connection_args,
			[
				'customFieldLike'          => [
					'type'        => CustomFieldFilterInput::$type,
					'description' => __( 'Include events that have a specified custom field value.', 'wp-graphql-tec' ),
				],
				'customFieldGreaterThan'   => [
					'type'        => CustomFieldFilterInput::$type,
					'description' => __( 'Include events that have a specified custom field greater than the specified value.', 'wp-graphql-tec' ),
				],
				'customFieldLessThan'      => [
					'type'        => CustomFieldFilterInput::$type,
					'description' => __( 'Include events that have a specified custom field less than the specified value.', 'wp-graphql-tec' ),
				],
				'customFieldBetween'       => [
					'type'        => CustomFieldRangeFilterInput::$type,
					'description' => __( 'Include events that have a specified custom field between than the specified values. Inclusive.', 'wp-graphql-tec' ),
				],
				'geolocation'              => [
					'type'        => GeolocationFilterInput::$type,
					'description' => __( 'Whether to fetch events related to Venues that have geolocation.', 'wp-graphql-tec' ),
				],
				'hasGeolocation'           => [
					'type'        => 'Boolean',
					'description' => __( 'Whether to fetch events related to Venues that have geolocation.', 'wp-graphql-tec' ),
				],
				'hasSubsequentRecurrences' => [
					'type'        => 'Boolean',
					'description' => __( 'Whether subsequent event recurrences are displayed. Defaults to settings.', 'wp-graphql-tec' ),
				],
				'inAnySeries'              => [
					'type'        => 'Boolean',
					'description' => __( 'Include events based on whether they are part of ANY series', 'wp-graphql-tec' ),
				],
				'inSeries'                 => [
					'type'        => 'Int',
					'description' => __( 'Include events that are in the series of the provided parent post ID.', 'wp-graphql-tec' ),
				],
				'near'                     => [
					'type'        => NearFilterInput::$type,
					'description' => __( 'Filters events to include only those that are geographically close to the provided address within a certain distance. This filter will be ignored if the address cannot be resolved to a set of latitude and longitude coordinates.', 'wp-graphql-tec' ),
				],
				'relatedTo'                => [
					'type'        => 'Int',
					'description' => __( 'Filters events to include those that are only related to a specific post database ID', 'wp-graphql-tec' ),
				],
			]
		);
	}

}
