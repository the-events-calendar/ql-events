<?php

use GraphQLRelay\Relay;

class Organizer_Helper extends WCG_Helper {
	protected function __construct() {
		$this->node_type = 'tribe_organizer';

		parent::__construct();
	}

	public function print_query( $id ) {
		$data = get_post( $id );

		return array(
			'email'   => ! empty( get_post_meta( $data->ID, '_OrganizerEmail', true ) )
				? get_post_meta( $data->ID, '_OrganizerEmail', true )
				: null,
			'website' => ! empty( get_post_meta( $data->ID, '_OrganizerWebsite', true ) )
				? get_post_meta( $data->ID, '_OrganizerWebsite', true )
				: null,
			'phone'     => ! empty( get_post_meta( $data->ID, '_OrganizerPhone', true ) )
				? get_post_meta( $data->ID, '_OrganizerPhone', true )
				: null,
		);
	}

	public function print_failed_query( $id ) {
		return array();
	}
}
