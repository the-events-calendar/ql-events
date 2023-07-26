<?php
/**
 * WPObject Type - Event
 *
 * Registers "Event" WPObject type and queries
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use Tribe__Events__JSON_LD__Event as JSON_LD;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\QL_Events\QL_Events;

/**
 * Class - Event_Type
 */
class Event_Type {
	/**
	 * Registers TEC core "Event" type fields.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register_fields() {
		register_graphql_fields(
			'Event',
			[
				'allDay'           => [
					'type'        => 'Boolean',
					'description' => __( 'Does the event last all day?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventAllDay', true );
						return ! is_null( $value ) ? $value : null;
					},
				],
				'startDate'        => [
					'type'        => 'String',
					'description' => __( 'Event start date', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventStartDate', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'endDate'          => [
					'type'        => 'String',
					'description' => __( 'Event end date', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventEndDate', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'duration'         => [
					'type'        => 'Float',
					'description' => __( 'Event duration', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventDuration', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'venue'            => [
					'type'        => 'Venue',
					'description' => __( 'Event venue', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$venue_id = get_post_meta( $source->ID, '_EventVenueID', true );
						return ! empty( $venue_id ) ? DataSource::resolve_post_object( $venue_id, $context ) : null;
					},
				],
				'showMapLink'      => [
					'type'        => 'Boolean',
					'description' => __( 'Show event map link?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventShowMapLink', true );
						return ! is_null( $value ) ? $value : null;
					},
				],
				'showMap'          => [
					'type'        => 'Boolean',
					'description' => __( 'Show event map?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventShowMap', true );
						return ! is_null( $value ) ? $value : null;
					},
				],
				'currencySymbol'   => [
					'type'        => 'String',
					'description' => __( 'Event currency symbol', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCurrencySymbol', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'currencyPosition' => [
					'type'        => 'String',
					'description' => __( 'Event currency position', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCurrencyPosition', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'cost'             => [
					'type'        => 'String',
					'description' => __( 'Event cost', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCost', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'costMin'          => [
					'type'        => 'String',
					'description' => __( 'Event minimum cost', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCostMin', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'costMax'          => [
					'type'        => 'String',
					'description' => __( 'Event maximum cost', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCostMax', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'url'              => [
					'type'        => 'String',
					'description' => __( 'Event URL', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventURL', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'phone'            => [
					'type'        => 'String',
					'description' => __( 'Event contact phone', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventPhone', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'hideFromUpcoming' => [
					'type'        => 'Boolean',
					'description' => __( 'Hide from event listing?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventHideFromUpcoming', true );
						return ! is_null( $value ) ? $value : null;
					},
				],
				'timezone'         => [
					'type'        => 'String',
					'description' => __( 'Event timezone', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventTimezone', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'timezoneAbbr'     => [
					'type'        => 'String',
					'description' => __( 'Event timezone abbreviation', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventTimezoneAbbr', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'origin'           => [
					'type'        => 'String',
					'description' => __( 'Event origin', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventOrigin', true );
						return ! empty( $value ) ? $value : null;
					},
				],
				'featured'         => [
					'type'        => 'Boolean',
					'description' => __( 'Is event featured?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_tribe_featured', true );
						return ! is_null( $value ) ? $value : null;
					},
				],
				'linkedData'       => [
					'type'        => 'EventLinkedData',
					'description' => __( 'Event JSON-LD object', 'ql-events' ),
					'resolve'     => function( $source ) {
						$instance = JSON_LD::instance();
						$data     = $instance->get_data( $source->ID );
						return ! empty( $data[ $source->ID ] ) ? $data[ $source->ID ] : null;
					},
				],
			]
		);
	}

	/**
	 * Registers TEC pro "Event" type fields.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register_pro_fields() {
		register_graphql_fields(
			'Event',
			[
				'recurring'      => [
					'type'        => 'Boolean',
					'description' => __( 'Is this a recurring event?', 'ql-events' ),
					'resolve'     => function( $source ) {
						if ( ! is_callable( '\tribe_is_recurring_event' ) ) {
							return null;
						}

						return tribe_is_recurring_event( $source->ID );
					},
				],
				'startDates'     => [
					'type'        => [ 'list_of' => 'String' ],
					'args'        => [
						'filter' => [ 'type' => 'DateQueryInput' ],
					],
					'description' => __( 'Recurrence events start dates', 'ql-events' ),
					'resolve'     => function( $source, array $args ) {
						if ( ! is_callable( '\tribe_get_recurrence_start_dates' ) ) {
							return null;
						}

						$dates = tribe_get_recurrence_start_dates( $source->ID );

						if ( ! empty( $args['filter'] ) ) {
							$query = \WPGraphQL\QL_Events\Data\Connection\Event_Connection_Resolver::date_query_input_to_meta_query(
								$args['filter'],
								''
							);
							$dates = array_filter(
								$dates,
								function( $date ) use ( $query ) {
									$left_date  = strtotime( $date );
									$right_date = strtotime( $query['value'] );

									$compare = $query['compare'];
									switch ( $compare ) {
										case '=':
											return $left_date === $right_date;
										case '>':
											return $left_date > $right_date;
										case '<':
											return $left_date < $right_date;
									}
								}
							);
						}

						return $dates;
					},
				],
				'recurrenceText' => [
					'type'        => 'String',
					'args'        => [
						'format' => [ 'type' => 'PostObjectFieldFormatEnum' ],
					],
					'description' => __( 'Recurrence text', 'ql-events' ),
					'resolve'     => function( $source, array $args ) {
						if ( ! is_callable( '\tribe_get_recurrence_text' ) ) {
							return null;
						}

						$text = tribe_get_recurrence_text( $source->ID );
						if ( ! empty( $args['format'] ) && 'raw' === $args['format'] ) {
							$text = wp_strip_all_tags( html_entity_decode( $text ) );
						}

						return $text;
					},
				],
			]
		);
	}

	/**
	 * Registers TEC events virtual "Event" type fields.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public static function register_virtual_fields() {
		register_graphql_fields(
			'Event',
			[
				'isVirtual'               => [
					'type'        => 'Boolean',
					'description' => __( 'Is this a virtual event?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual;
						return ! is_null( $value ) ? $value : false;
					},
				],
				'virtualUrl'              => [
					'type'        => 'String',
					'description' => __( "The event's virtual URL.", 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_url;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualEmbedVideo'       => [
					'type'        => 'Boolean',
					'description' => __( "Whether to show an event's video embed.", 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_embed_video;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualLinkedButton'     => [
					'type'        => 'Boolean',
					'description' => __( "Whether to show an event's linked button", 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_linked_button;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualLinkedButtonText' => [
					'type'        => 'String',
					'description' => __( "The virtual linked button text. Defaults to 'Watch'. ", 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_linked_button_text;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualShowEmbedAt'      => [
					'type'        => 'EventsVirtualShowEmbedAtEnum',
					'description' => __( 'The time to start displaying the video embed', 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_show_embed_at;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualShowEmbedTo'      => [
					'type'        => [
						'list_of' => 'EventsVirtualShowEmbedToEnum',
					],
					'description' => __( 'The user type (logged in or all) to display the video embed to.', 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_show_embed_to;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualShowLeadUp'       => [
					'type'        => 'Number',
					'description' => __( 'The lead-up for the event embed/link button in minutes.', 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_show_lead_up;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualIsLinkable'       => [
					'type'        => 'Boolean',
					'description' => __( 'Whether the event has a URL to show (link/embed).', 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_is_linkable;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualIsImmediate'      => [
					'type'        => 'Boolean',
					'description' => __( 'Is the event set to show the embed immediately?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_is_immediate;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualShouldShowEmbed'  => [
					'type'        => 'Boolean',
					'description' => __( 'Is the event ready to show the embed now?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_should_show_embed;
						return ! is_null( $value ) ? $value : null;
					},
				],
				'virtualShouldShowLink'   => [
					'type'        => 'Boolean',
					'description' => __( 'Is the event ready to show the link now?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$event = tribe_get_event( $source->ID );
						if ( ! $event ) {
							return null;
						}

						$value = $event->virtual_should_show_link;
						return ! is_null( $value ) ? $value : null;
					},
				],
			]
		);
	}
}
