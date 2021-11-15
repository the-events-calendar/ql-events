<?php
/**
 * GraphQL Object Type - Venue
 *
 * @package WPGraphQL\TEC\Events\Type\WPObject
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\WPObject;

use WPGraphQL\TEC\Events\Type\WPObject\VenueCoordinates;
use WPGraphQL\TEC\Events\Type\WPObject\VenueLinkedData;

/**
 * Class - Venue
 */
class Venue {
	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'Venue';

	/**
	 * The type used by WordPress.
	 *
	 * @var string
	 */
	public static $wp_type = 'tribe_venue';

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
				'address'       => [
					'type'        => 'String',
					'description' => __( 'The venue street address.', 'wp-graphql-tec' ),
				],
				'city'          => [
					'type'        => 'String',
					'description' => __( 'The venue city.', 'wp-graphql-tec' ),
				],
				// @todo Enable for TEC Pro.
				// phpcs:disable
				/*
				'coordinates'   => [
					'type'        => VenueCoordinates::$type,
					'description' => __( 'The venue coordinates.', 'wp-graphql-tec' ),
				],
				*/
				// phpcs:enable
				'country'       => [
					'type'        => 'String',
					'description' => __( 'The venue country.', 'wp-graphql-tec' ),
				],
				'linkedData'    => [
					'type'        => VenueLinkedData::$type,
					'description' => __( 'The JsonLD data for the organizer.', 'wp-graphql-tec' ),
				],
				'mapLink'       => [
					'type'        => 'String',
					'description' => __( 'The external link to the venue map directions.', 'wp-graphql-tec' ),
				],
				'phone'         => [
					'type'        => 'String',
					'description' => __( 'The venue phone number.', 'wp-graphql-tec' ),
				],
				'province'      => [
					'type'        => 'String',
					'description' => __( 'The venue province.', 'wp-graphql-tec' ),
				],
				'showMap'       => [
					'type'        => 'Boolean',
					'description' => __( 'Whether to display the event map.', 'wp-graphql-tec' ),
				],
				'showMapLink'   => [
					'type'        => 'Boolean',
					'description' => __( 'Whether to display a link to the Map.', 'wp-graphql-tec' ),
				],
				'state'         => [
					'type'        => 'String',
					'description' => __( 'The venue state.', 'wp-graphql-tec' ),
				],
				'stateProvince' => [
					'type'        => 'String',
					'description' => __( 'The venue state or province.', 'wp-graphql-tec' ),
				],
				'website'       => [
					'type'        => 'String',
					'description' => __( 'The venue website.', 'wp-graphql-tec' ),
				],
				'zip'           => [
					'type'        => 'String',
					'description' => __( 'The venue zip code.', 'wp-graphql-tec' ),
				],
			]
		);
	}
}
