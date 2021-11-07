<?php
/**
 * Venue Model class
 *
 * @package \WPGraphQL\TEC\Models
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Post;

/**
 * Class - Event
 */
class Venue extends Post {
	/**
	 * {@inheritDoc}
	 */
	public function setup() {
		remove_action( 'the_post', [ tribe( \Tribe\Events\Views\V2\Hooks::class ), 'manage_sensitive_info' ] );

		parent::setup();
	}
	/**
	 * Initializes the Event object.
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			parent::init();

			$fields = [
				'address'       => function() : ?string {
					return tribe_get_address( $this->data->ID ) ?: null;
				},
				// @Todo only available on pro.
				'coordinates'   => function() : ?array {
					return tribe_get_coordinates( $this->data->ID ) ?: null;
				},
				'country'       => function() : ?string {
					return tribe_get_country( $this->data->ID ) ?: null;
				},
				'city'          => function() : ?string {
					return tribe_get_city( $this->data->ID ) ?: null;
				},
				'id'            => function() : ?string {
					return ! empty( $this->data->ID ) ? Relay::toGlobalId( 'tribe_venue', (string) $this->data->ID ) : null;
				},
				'linkedData'    => function() {
					// TEC delivers this as an array with the eventId as the key.
					$value = tribe( 'tec.json-ld.venue' )->get_data( $this->data->ID )[ $this->data->ID ];
					return $value ?: null;
				},
				'mapLink'       => function() : ?string {
					return tribe_get_map_link( (string) $this->data->ID ) ?: null;
				},
				'phone'         => function() : ?string {
					return tribe_get_phone( $this->data->ID ) ?: null;
				},
				'province'      => function() : ?string {
					return tribe_get_province( $this->data->ID ) ?: null;
				},
				'showMap'       => function() : bool {
					$value = get_post_meta( $this->data->ID, '_VenueShowMap', true );
					return ! is_null( $value ) ? $value : null;
				},
				'showMapLink'   => function() : bool {
					$value = get_post_meta( $this->data->ID, '_VenueShowMapLink', true );
					return ! is_null( $value ) ? $value : null;
				},
				'state'         => function() : ?string {
					return tribe_get_state( $this->data->ID ) ?: null;
				},
				'stateProvince' => function() : ?string {
					return tribe_get_stateprovince( $this->data->ID ) ?: null;
				},
				'website'       => function() : ?string {
					return tribe_get_venue_website_url( $this->data->ID ) ?: null;
				},
				'zip'           => function() : ?string {
					return tribe_get_zip( $this->data->ID ) ?: null;
				},
			];

			$this->fields = array_merge( $this->fields, $fields );
		}
	}
}
