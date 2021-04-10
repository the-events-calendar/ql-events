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

/**
 * Class - Event_Type
 */
class Event_Type {
	/**
	 * Registers "Event" type fields.
	 */
	public static function register_fields() {
		self::register_core_fields();
		self::register_pro_fields();
	}

	public static function register_core_fields() {
		register_graphql_fields(
			'Event',
			array(
				'allDay'           => array(
					'type'        => 'Boolean',
					'description' => __( 'Does the event last all day?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventAllDay', true );
						return ! is_null( $value ) ? $value : null;
					},
				),
				'startDate'        => array(
					'type'        => 'String',
					'description' => __( 'Event start date', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventStartDate', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'endDate'          => array(
					'type'        => 'String',
					'description' => __( 'Event end date', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventEndDate', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'duration'         => array(
					'type'        => 'Float',
					'description' => __( 'Event duration', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventDuration', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'venue'            => array(
					'type'        => 'Venue',
					'description' => __( 'Event venue', 'ql-events' ),
					'resolve'     => function( $source, array $args, AppContext $context ) {
						$venue_id = get_post_meta( $source->ID, '_EventVenueID', true );
						return ! empty( $venue_id ) ? DataSource::resolve_post_object( $venue_id, $context ) : null;
					},
				),
				'showMapLink'      => array(
					'type'        => 'Boolean',
					'description' => __( 'Show event map link?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventShowMapLink', true );
						return ! is_null( $value ) ? $value : null;
					},
				),
				'showMap'          => array(
					'type'        => 'Boolean',
					'description' => __( 'Show event map?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventShowMap', true );
						return ! is_null( $value ) ? $value : null;
					},
				),
				'currencySymbol'   => array(
					'type'        => 'String',
					'description' => __( 'Event currency symbol', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCurrencySymbol', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'currencyPosition' => array(
					'type'        => 'String',
					'description' => __( 'Event currency position', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCurrencyPosition', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'cost'             => array(
					'type'        => 'String',
					'description' => __( 'Event cost', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCost', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'costMin'          => array(
					'type'        => 'String',
					'description' => __( 'Event minimum cost', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCostMin', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'costMax'          => array(
					'type'        => 'String',
					'description' => __( 'Event maximum cost', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventCostMax', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'url'              => array(
					'type'        => 'String',
					'description' => __( 'Event URL', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventURL', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'phone'            => array(
					'type'        => 'String',
					'description' => __( 'Event contact phone', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventPhone', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'hideFromUpcoming' => array(
					'type'        => 'Boolean',
					'description' => __( 'Hide from event listing?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventHideFromUpcoming', true );
						return ! is_null( $value ) ? $value : null;
					},
				),
				'timezone'         => array(
					'type'        => 'String',
					'description' => __( 'Event timezone', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventTimezone', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'timezoneAbbr'     => array(
					'type'        => 'String',
					'description' => __( 'Event timezone abbreviation', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventTimezoneAbbr', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'origin'           => array(
					'type'        => 'String',
					'description' => __( 'Event origin', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_EventOrigin', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'featured'         => array(
					'type'        => 'Boolean',
					'description' => __( 'Is event featured?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_tribe_featured', true );
						return ! is_null( $value ) ? $value : null;
					},
				),
				'linkedData'       => array(
					'type'        => 'EventLinkedData',
					'description' => __( 'Event JSON-LD object', 'ql-events' ),
					'resolve'     => function( $source ) {
						$instance = JSON_LD::instance();
						$data     = $instance->get_data( $source->ID );
						return ! empty( $data[ $source->ID ] ) ? $data[ $source->ID ] : null;
					},
				),
			)
		);
	}

	public static function register_pro_fields() {
		register_graphql_fields(
			'Event',
			array(
				'recurring'        => array(
					'type'        => 'Boolean',
					'description' => __( 'Is this a recurring event?', 'ql-events' ),
					'resolve'     => function( $source ) {
						if ( ! is_callable( '\tribe_is_recurring_event' ) ) {
							return null;
						}

						return tribe_is_recurring_event( $source->ID );
					},
				),
				'startDates'       => array(
					'type'        => array( 'list_of' => 'String' ),
					'args'        => array(
						'filter' => array( 'type' => 'DateQueryInput' ),
					),
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
									switch( $compare ) {
										case '=':
											return $left_date === $right_date;
										case '>':
											return $left_date > $right_date;
										case '<':
											return $left_date < $right_date;
									}
								},
							);
						}

						return $dates;
					}
				),
				'recurrenceText'    => array(
					'type'        => 'String',
					'args'        => array(
						'format' => array( 'type' => 'PostObjectFieldFormatEnum' )
					),
					'description' => __( 'Recurrence text', 'ql-events' ),
					'resolve'     => function( $source, array $args ) {
						if ( ! is_callable( '\tribe_get_recurrence_text' ) ) {
							return null;
						}

						$text = tribe_get_recurrence_text( $source->ID );
						if ( ! empty( $args['format'] ) && 'raw' === $args['format'] ) {
							$text = strip_tags( html_entity_decode( $text ) );
						}

						return $text;
					}
				),
			)
		);

		
		/**
		 * Register custom meta fields.
		 */ 
		$custom_fields = tribe_get_option( 'custom-fields');

		if ( is_array( $custom_fields ) ) {
			foreach ( $custom_fields as $field ) {
				// Use label as graphQL key, instead of _ecp_custom_{#}.
				$key = 'custom' . ( str_replace( ' ', '', ucwords( $field['label'] ) ) );

				register_graphql_field(
					'Event', 
					$key,
					array(
						'type'        => 'String',
						'description' => $field['label'],
						'resolve'     => function ( $source ) use( $field ) {
							get_post_meta( $source->ID, $field['name'], true);
						}
					)
				);
			}
		}
	}
}
