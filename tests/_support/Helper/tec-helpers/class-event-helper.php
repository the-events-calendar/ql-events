<?php

use GraphQLRelay\Relay;

class Event_Helper extends WCG_Helper {
	protected function __construct() {
		$this->node_type = 'tribe_events';

		parent::__construct();
	}

	public function print_query( $id ) {
		$data = get_post( $id );

		return array(
			'allDay'           => ! empty( get_post_meta( $data->ID, '_EventAllDay', true ) )
				? ! is_null( get_post_meta( $data->ID, '_EventAllDay', true ) )
				: false, 
			'startDate'        => get_post_meta( $data->ID, '_EventStartDate', true ),
			'endDate'          => get_post_meta( $data->ID, '_EventEndDate', true ),
			'duration'         => (float) get_post_meta( $data->ID, '_EventDuration', true ),
			'showMapLink'      => ! empty( get_post_meta( $data->ID, '_EventShowMapLink', true ) )
				? get_post_meta( $data->ID, '_EventShowMapLink', true )
				: false,
			'showMap'          => ! empty( get_post_meta( $data->ID, '_EventShowMap', true ) )
				? get_post_meta( $data->ID, '_EventShowMap', true )
				: false,
			'currencySymbol'   => ! empty( get_post_meta( $data->ID, '_EventCurrencySymbol', true ) )
				? get_post_meta( $data->ID, '_EventCurrencySymbol', true )
				: null,
			'currencyPosition' => ! empty( get_post_meta( $data->ID, '_EventCurrencyPosition', true ) )
				? get_post_meta( $data->ID, '_EventCurrencyPosition', true )
				: null,
			'cost'             => ! empty( get_post_meta( $data->ID, '_EventCost', true ) )
				? get_post_meta( $data->ID, '_EventCost', true )
				: null,
			'costMin'          => ! empty( get_post_meta( $data->ID, '_EventCostMin', true ) )
				? get_post_meta( $data->ID, '_EventCostMin', true )
				: null,
			'costMax'          => ! empty( get_post_meta( $data->ID, '_EventCostMax', true ) )
				? get_post_meta( $data->ID, '_EventCostMax', true )
				: null,
			'url'              => ! empty( get_post_meta( $data->ID, '_EventURL', true ) )
				? get_post_meta( $data->ID, '_EventURL', true )
				: null,
			'phone'            => ! empty( get_post_meta( $data->ID, '_EventPhone', true ) )
				? get_post_meta( $data->ID, '_EventPhone', true )
				: null,
			'hideFromUpcoming' => ! empty( get_post_meta( $data->ID, '_EventHideFromUpcoming', true ) )
				? get_post_meta( $data->ID, '_EventHideFromUpcoming', true )
				: false,
			'timezone'         => ! empty( get_post_meta( $data->ID, '_EventTimezone', true ) )
				? get_post_meta( $data->ID, '_EventTimezone', true )
				: null,
			'timezoneAbbr'     => ! empty( get_post_meta( $data->ID, '_EventTimezoneAbbr', true ) )
				? get_post_meta( $data->ID, '_EventTimezoneAbbr', true )
				: null,
			'origin'           => ! empty( get_post_meta( $data->ID, '_EventOrigin', true ) )
				? get_post_meta( $data->ID, '_EventOrigin', true )
				: null,
			'featured'         => ! empty( get_post_meta( $data->ID, '_tribe_feature', true ) )
				? get_post_meta( $data->ID, '_tribe_feature', true )
				: false,
			'venue'           => array(
				'id' => Relay::toGlobalId( 
					'tribe_venue',
					get_post_meta( $data->ID, '_EventVenueID', true )
				),
			),
		);
	}

	public function print_failed_query( $id ) {
		return array();
	}
}
