<?php
/**
 * Extends the VenueHelper.
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
class VenueHelper {

	/**
	 * Extends event connection with where args.
	 *
	 * @param array $connection_args .
	 */
	public static function add_where_args_to_venues_connection( array $connection_args ) : array {
		return array_merge(
			$connection_args,
			[
				'geolocation'    => [
					'type'        => GeolocationFilterInput::$type,
					'description' => __( 'Whether to fetch events related to Venues that have geolocation.', 'wp-graphql-tec' ),
				],
				'hasGeolocation' => [
					'type'        => 'Boolean',
					'description' => __( 'Whether to fetch events related to Venues that have geolocation.', 'wp-graphql-tec' ),
				],
				'near'           => [
					'type'        => NearFilterInput::$type,
					'description' => __( 'Filters events to include only those that are geographically close to the provided address within a certain distance. This filter will be ignored if the address cannot be resolved to a set of latitude and longitude coordinates.', 'wp-graphql-tec' ),
				],
			]
		);
	}

}
