<?php
/**
 * Venue Model class
 *
 * @package \WPGraphQL\TEC\Events\Model
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Post;
use WP_Post;
use Exception;

/**
 * Class - Event
 */
class Venue extends Post {
	/**
	 * Venue constructor.
	 *
	 * @param WP_Post $post the post object.
	 *
	 * @throws Exception .
	 */
	public function __construct( WP_Post $post ) {
		if ( empty( $post->post_type ) || 'tribe_venue' !== $post->post_type ) {
			throw new Exception( __( 'The object returned is not a venue.', 'wp-graphql-tec' ) );
		}

		$post = tribe_get_venue_object( $post );

		parent::__construct( $post );
	}

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
				'address'       => fn() : ?string => ! empty( $this->data->address ) ? $this->data->adresss : null,
				'city'          => fn() : ?string => ! empty( $this->data->city ) ? $this->data->city : null,
				'country'       => fn() : ?string => ! empty( $this->data->country ) ? $this->data->country : null,
				'id'            => fn() : ?string => ! empty( $this->data->ID ) ? Relay::toGlobalId( $this->data->post_type, (string) $this->data->ID ) : null,
				'linkedData'    => function() {
					// TEC delivers this as an array with the eventId as the key.
					$value = tribe( 'tec.json-ld.venue' )->get_data( $this->data->ID )[ $this->data->ID ];
					return $value ?: null;
				},
				'mapLink'       => fn() : ?string => ! empty( $this->data->directions_link ) ? $this->data->directions_link : null,
				'phone'         => fn() : ?string => ! empty( $this->data->phone ) ? $this->data->phone : null,
				'province'      => fn() : ?string => ! empty( $this->data->province ) ? $this->data->province : null,
				'hasMap'        => function() : bool {
					$value = get_post_meta( $this->data->ID, '_VenueShowMap', true );
					return ! is_null( $value ) ? $value : null;
				},
				'hasMapLink'    => function() : bool {
					$value = get_post_meta( $this->data->ID, '_VenueShowMapLink', true );
					return ! is_null( $value ) ? $value : null;
				},
				'state'         => fn() : ?string => ! empty( $this->data->state ) ? $this->data->state : null,
				'stateProvince' => fn() : ?string => ! empty( $this->data->state_province ) ? $this->data->state_province : null,
				'website'       => fn() : ?string => ! empty( $this->data->website ) ? $this->data->website : null,
				'zip'           => fn() : ?string => ! empty( $this->data->zip ) ? $this->data->zip : null,
			];

			$this->fields = array_merge( $this->fields, $fields );

			/**
			 * Filters the model fields.
			 *
			 * Useful for adding fields to a model when an extension.
			 *
			 * @param array $fields The fields registered to the model.
			 * @param WP_Post $data The post data.
			 */
			$this->fields = apply_filters( 'graphql_tec_venue_model_fields', $this->fields, $this->data );
		}
	}
}
