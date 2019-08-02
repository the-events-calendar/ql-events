<?php

use GraphQLRelay\Relay;

class Venue_Helper extends WCG_Helper {
	protected function __construct() {
		$this->node_type = 'tribe_venue';

		parent::__construct();
	}

	public function create( $args = array() ) {
		
	}

	public function print_query( $id ) {
		$data = get_post( $id );

		return array(
			'country'       => ! empty( get_post_meta( $data->ID, '_VenueCountry', true ) )
				? get_post_meta( $data->ID, '_VenueCountry', true )
				: null,
			'address'       => ! empty( get_post_meta( $data->ID, '_VenueAddress', true ) )
				? get_post_meta( $data->ID, '_VenueAddress', true )
				: null,
			'city'          => ! empty( get_post_meta( $data->ID, '_VenueCity', true ) )
				? get_post_meta( $data->ID, '_VenueCity', true )
				: null,
			'stateProvince' => ! empty( get_post_meta( $data->ID, '_VenueStateProvince', true ) )
				? get_post_meta( $data->ID, '_VenueStateProvince', true )
				: null,
			'state'         => ! empty( get_post_meta( $data->ID, '_VenueState', true ) )
				? get_post_meta( $data->ID, '_VenueState', true )
				: null,
			'province'      => ! empty( get_post_meta( $data->ID, '_VenueProvince', true ) )
				? get_post_meta( $data->ID, '_VenueProvince', true )
				: null,
			'zip'           => ! empty( get_post_meta( $data->ID, '_VenueZip', true ) )
				? get_post_meta( $data->ID, '_VenueZip', true )
				: null,
			'phone'         => ! empty( get_post_meta( $data->ID, '_VenuePhone', true ) )
				? get_post_meta( $data->ID, '_VenuePhone', true )
				: null,
			'url'           => ! empty( get_post_meta( $data->ID, '_VenueURL', true ) )
				? get_post_meta( $data->ID, '_VenueURL', true )
				: null,
			'showMap'       => ! empty( get_post_meta( $data->ID, '_VenueShowMap', true ) )
				? get_post_meta( $data->ID, '_VenueShowMap', true )
				: false,
			'showMapLink'   => ! empty( get_post_meta( $data->ID, '_VenueShowMapLink', true ) )
				? get_post_meta( $data->ID, '_VenueShowMapLink', true )
				: false,
		);
	}

	public function print_failed_query( $id ) {
		return array();
	}
}
