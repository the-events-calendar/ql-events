<?php
/**
 * Event Model class
 *
 * @package \WPGraphQL\TEC\Events\Model
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Events\Model;

use Exception;
use GraphQLRelay\Relay;
use Tribe__Date_Utils as DateUtils;
use Tribe__Events__Timezones as Timezones;
use WP_Post;
use WPGraphQL\Model\Post;
/**
 * Class - Event
 */
class Event extends Post {
	/**
	 * Event constructor.
	 *
	 * @param WP_Post $post the post object.
	 *
	 * @throws Exception .
	 */
	public function __construct( WP_Post $post ) {
		if ( empty( $post->post_type ) || 'tribe_events' !== $post->post_type ) {
			throw new Exception( __( 'The object returned is not an Event.', 'wp-graphql-tec' ) );
		}

		$post = tribe_get_event( $post );

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
				'cost'                 => fn() : ?string => ! empty( $this->data->cost ) ? $this->data->cost : null,
				'costMax'              => function() : ?string {
					$value = tribe_get_cost( $this->data->ID );
					$value = ! empty( $value ) ? tribe( 'tec.cost-utils' )->get_maximum_cost( $value ) : null;

					return $value ?: null;
				},
				'costMin'              => function() : ?string {
					$value = tribe_get_cost( $this->data->ID );
					$value = ! empty( $value ) ? tribe( 'tec.cost-utils' )->get_minimum_cost( $value ) : null;

					return $value ?: null;
				},
				'currencyPosition'     => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventCurrencyPosition', true );
					return $value ?: null;
				},
				'currencySymbol'       => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventCurrencySymbol', true );
					return $value ?: null;
				},
				'duration'             => fn() : ?int => $this->data->duration ?? null,
				'endDate'              => fn() : ?string => ! empty( $this->data->end_date ) ? $this->data->end_date : null,
				'endDateUTC'           => fn() : ?string => ! empty( $this->data->end_date_utc ) ? $this->data->end_date_utc : null,
				'eventUrl'             => function() : ?string {
					return tribe_get_event_meta( $this->data->ID, '_EventURL', true ) ?: null;
				},
				'id'                   => fn() : ?string => ! empty( $this->data->ID ) ? Relay::toGlobalId( $this->data->post_type, (string) $this->data->ID ) : null,
				'isAllDay'             => fn() : bool => ! empty( $this->data->all_day ),
				'isFeatured'           => function() : ?bool {
					return tribe( 'tec.featured_events' )->is_featured( $this->data->ID );
				},
				'isHiddenFromUpcoming' => fn() : bool => ! empty( tribe_get_event_meta( $this->data->ID, '_EventHideFromUpcoming', true ) ),
				'isMultiday'           => fn() : bool => ! empty( $this->data->multiday ),
				'isSticky'             => fn() : bool => ! empty( $this->data->sticky ),
				'isPast'               => fn() : bool => tribe_is_past_event( $this->data ),
				'organizerDatabaseIds' => fn() : ?array => tribe_get_organizer_ids( $this->data->ID ) ?: null,
				'origin'               => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventOrigin', true );
					return $value ?: null;
				},
				'scheduleDetails'      => function() : ?string {
					return tribe_events_event_schedule_details( $this->data->ID, '', '', false ) ?: null;
				},
				'scheduleDetailsShort' => function() : ?string {
					return tribe_events_event_short_schedule_details( $this->data->ID, '', '', false ) ?: null;
				},
				'hasMap'               => function() : ?bool {
					$value = tribe_get_event_meta( $this->data->ID, '_EventShowMap', true );
					return ! is_null( $value ) ? $value : null;
				},
				'hasMapLink'           => function() : ?bool {
					$value = tribe_get_event_meta( $this->data->ID, '_EventShowMapLink', true );
					return ! is_null( $value ) ? $value : null;
				},
				'startDate'            => fn() : ?string => ! empty( $this->data->start_date ) ? $this->data->start_date : null,
				'startDateUTC'         => fn() : ?string => ! empty( $this->data->start_date_utc ) ? $this->data->start_date_utc : null,
				'timezone'             => function() : ?string {
					return Timezones::get_event_timezone_string( $this->data->ID ) ?: null;
				},
				'timezoneAbbr'         => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventTimezoneAbbr', true );
					return $value ?: null;
				},
				'venueDatabaseId'      => function() : ?int {
					return tribe_get_venue_id( $this->data->ID ) ?: null;
				},
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
			$this->fields = apply_filters( 'graphql_tec_event_model_fields', $this->fields, $this->data );
		}
	}
}
