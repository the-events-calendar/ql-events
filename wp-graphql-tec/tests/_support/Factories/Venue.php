<?php

namespace WPGraphQL\TEC\Test\Factories;

use Tribe__Events__Main as Main;

class Venue extends \WP_UnitTest_Factory_For_Post {

	/**
	 * @var array An array containing some pre-compiled locations data.
	 */
	protected $locations
		= [
			'new_york' => [
				'_VenueAddress'       => '939 Lexington Ave',
				'_VenueCity'          => 'New York',
				'_VenueCountry'       => 'United States',
				'_VenueProvince'      => '',
				'_VenueState'         => 'NY',
				'_VenueZip'           => '10065',
				'_VenuePhone'         => '1-234-576-8910',
				'_VenueURL'           => 'http://example.com',
				'_VenueShowMap'       => true,
				'_VenueShowMapLink'   => true,
				'_VenueStateProvince' => 'NY',
			],
			'paris'    => [
				'_VenueAddress'       => '37 Rue de la Bûcherie',
				'_VenueCity'          => 'Paris',
				'_VenueCountry'       => 'France',
				'_VenueProvince'      => 'Paris',
				'_VenueState'         => '',
				'_VenueZip'           => '75005',
				'_VenuePhone'         => '+1-234-567-8910',
				'_VenueURL'           => 'http://example.com',
				'_VenueShowMap'       => true,
				'_VenueShowMapLink'   => true,
				'_VenueStateProvince' => 'Paris',
			],
		];

	function create_object( $args ) {
		$args['post_type'] = Main::VENUE_POST_TYPE;

		$defaults = [
			'meta_input' => [
				'_EventShowMap'       => true,
				'_EventShowMapLink'   => true,
				'_VenueAddress'       => $this->locations['new_york']['_VenueAddress'],
				'_VenueCity'          => $this->locations['new_york']['_VenueCity'],
				'_VenueCountry'       => $this->locations['new_york']['_VenueCountry'],
				'_VenueProvince'      => $this->locations['new_york']['_VenueProvince'],
				'_VenueState'         => $this->locations['new_york']['_VenueState'],
				'_VenueZip'           => $this->locations['new_york']['_VenueZip'],
				'_VenuePhone'         => $this->locations['new_york']['_VenuePhone'],
				'_VenueURL'           => $this->locations['new_york']['_VenueURL'],
				'_VenueStateProvince' => $this->locations['new_york']['_VenueStateProvince'],
			],
		];

		if ( isset( $args['location'] ) && isset( $this->locations[ $args['location'] ] ) ) {
			$defaults['meta_input'] = array_merge( $defaults['meta_input'], $this->locations[ $args['location'] ] );
			unset( $args['location'] );
		}

		if ( isset( $args['meta_input'] ) ) {
			$defaults['meta_input'] = array_merge( $defaults['meta_input'], $args['meta_input'] );
			unset( $args['meta_input'] );
		}

		return parent::create_object( array_merge( $defaults, $args ) );
	}
}
