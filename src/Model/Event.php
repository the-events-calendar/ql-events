<?php
/**
 * Event Model class
 *
 * @package \WPGraphQL\TEC\Models
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Model;

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
		parent::__construct( $post );

		if ( ! isset( $this->data->post_type ) || 'tribe_events' !== $this->data->post_type ) {
			throw new Exception( __( 'The object returned is not an Event.', 'wp-graphql-tec' ) );
		}

		$this->data = tribe_get_event( $this->data );
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
				'cost'             => function() : ?string {
					return tribe_get_cost( $this->data->ID ) ?: null;
				},
				'costMax'          => function() : ?string {
					$value = tribe_get_cost( $this->data->ID );
					$value = ! empty( $value ) ? tribe( 'tec.cost-utils' )->get_maximum_cost( $value ) : null;

					return $value ?: null;
				},
				'costMin'          => function() : ?string {
					$value = tribe_get_cost( $this->data->ID );
					$value = ! empty( $value ) ? tribe( 'tec.cost-utils' )->get_minimum_cost( $value ) : null;

					return $value ?: null;
				},
				'currencySymbol'   => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventCurrencySymbol', true );
					return $value ?: null;
				},
				'currencyPosition' => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventCurrencyPosition', true );
					return $value ?: null;
				},
				'duration'         => function() : ?int {
					return isset( $this->data->duration ) ? ( $this->data->duration ?: null ) : null;
				},
				'endDate'          => function() : ?string {
					return tribe_get_end_date( $this->data->ID, true, DateUtils::DBDATETIMEFORMAT ) ?? null;
				},
				'endDateUTC'       => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventEndDateUTC', true );
					return $value ?: null;
				},
				'eventUrl'         => function() : ?string {
					return tribe_get_event_meta( $this->data->ID, '_EventURL', true ) ?: null;
				},
				'hideFromUpcoming' => function() : ?bool {
					$value = tribe_get_event_meta( $this->data->ID, '_EventHideFromUpcoming', true );
					return ! is_null( $value ) ? $value : null;
				},
				'id'               => function() : ?string {
					return ! empty( $this->data->ID ) ? Relay::toGlobalId( 'tribe_events', (string) $this->data->ID ) : null;
				},
				'isAllDay'         => function() : bool {
					return tribe_event_is_all_day( $this->data->ID );
				},
				'isFeatured'       => function() : ?bool {
					return tribe( 'tec.featured_events' )->is_featured( $this->data->ID );
				},
				'isSticky'         => function() : bool {
					return -1 === $this->data->menu_order;
				},
				'isMultiday'       => function() : bool {
					return tribe_event_is_multiday( $this->data->ID );
				},
				'organizerIds'     => function() : ?array {
					$organizer_ids = tribe_get_organizer_ids( $this->data->ID ) ?: null;
					return $organizer_ids;
				},
				'origin'           => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventOrigin', true );
					return $value ?: null;
				},
				'phone'            => function(): ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventPhone', true );
					return $value ?: null;
				},
				'scheduleDetails'  => function() : ?string {
					return tribe_events_event_schedule_details( $this->data->ID, '', '', false ) ?: null;
				},
				'showMap'          => function() : ?bool {
					$value = tribe_get_event_meta( $this->data->ID, '_EventShowMap', true );
					return ! is_null( $value ) ? $value : null;
				},
				'showMapLink'      => function() : ?bool {
					$value = tribe_get_event_meta( $this->data->ID, '_EventShowMapLink', true );
					return ! is_null( $value ) ? $value : null;
				},
				'startDate'        => function() : ?string {
					return tribe_get_start_date( $this->data->ID, true, DateUtils::DBDATETIMEFORMAT ) ?? null;
				},
				'startDateUTC'     => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventStartDateUTC', true );
					return $value ?: null;
				},
				'timezone'         => function() : ?string {
					return Timezones::get_event_timezone_string( $this->data->ID ) ?: null;
				},
				'timezoneAbbr'     => function() : ?string {
					$value = tribe_get_event_meta( $this->data->ID, '_EventTimezoneAbbr', true );
					return $value ?: null;
				},
				'venueId'          => function() : int {
					return tribe_get_venue_id( $this->data->ID );
				},
			];

			$this->fields = array_merge( $this->fields, $fields );
		}
	}
}
