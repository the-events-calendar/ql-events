<?php
/**
 * Test - Event Queries
 *
 * @package .
 * @sinse 0.0.1
 */

use GraphQLRelay\Relay;
use Tribe__Date_Utils as DateUtils;
use Tribe__Events__Timezones as Timezones;
use WPGraphQL\TEC\Test\TestCase\TecGraphQLTestCase;

/**
 * Class - EventQueriesTest
 */
class EventQueriesTest extends TecGraphQLTestCase {
	private int $event_id;
	private int $venue_id;
	private int $organizer_one_id;
	private int $organizer_two_id;

	/**
	 * {@inheritDoc}
	 */
	public function setUp() : void {
		// before.
		parent::setUp();
		$this->organizer_one_id = $this->factory->organizer->create();
		$this->organizer_two_id = $this->factory->organizer->create();
		$this->venue_id         = $this->factory->venue->create();
		$this->event_id         = $this->factory->event->create(
			[
				'venue'      => $this->venue_id,
				'organizers' => [
					$this->organizer_one_id,
					$this->organizer_two_id,
				],
			]
		);

		$this->clearSchema();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown() : void {
		wp_delete_post( $this->event_id );
		wp_delete_post( $this->venue_id );
		wp_delete_post( $this->organizer_one_id );
		wp_delete_post( $this->organizer_two_id );

		// Then...
		parent::tearDown();
	}

	public function testEventQueries() : void {
		$event     = $this->factory->event->get_object_by_id( $this->event_id );
		$global_id = Relay::toGlobalId( 'tribe_events', $this->event_id );

		$query = $this->get_query();

		$variables = [
			'id'     => $this->event_id,
			'idType' => 'DATABASE_ID',
		];
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		$expected = $this->get_expected_response( $event );

		// Test with Database ID.
		$this->assertArrayNotHasKey( 'errors', $response, 'Query by DatabaseID has errors' );
		$this->assertQuerySuccessful( $response, $expected );

		// Test with Database ID.
		$variables = [
			'id'     => $global_id,
			'idType' => 'ID',
		];
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, 'Query by GlobalID has errors' );
		$this->assertQuerySuccessful( $response, $expected );
	}

	public function testEventQueryArgs() : void {
		// $event_ids = [
		// $this->event_id,
		// $this->factory->event->create(),
		// ];

		$query = '
			query testQueryArgs( $id: RootQueryToEventConnectionWhereArgs ){
				events( where: $where ) {
					nodes {
						databaseId
					}
				}
			}
		';

		$variables = [
			'where' => [
				'organizerId' => $this->organizer_one_id,
			],
		];

		$expected = [
			$this->expectedObject(
				'events',
				[
					$this->expectedNode(
						'0',
						[
							$this->expectedField( 'databaseId', $this->eventId ),
						]
					),
				]
			),
		];

		$this->markTestIncomplete(
			'This test has not been implemented yet. Requires https://github.com/wp-graphql/wp-graphql/pull/2141.'
		);
	}

	private function get_query() : string {
		return '
			query getEvent( $id: ID!, $idType: EventIdType ) {
				event( id: $id, idType: $idType ) {
					cost
					costMax
					costMin
					currencyPosition
					currencySymbol
					duration
					endDate
					endDateUTC
					eventUrl
					hideFromUpcoming
					isAllDay
					isFeatured
					isMultiday
					isSticky
					linkedData {
						context
						endDate
						description
						image
						location {
							type
						}
						name
						offers {
							type
						}
						organizer {
							type
						}
						performer
						startDate
						type
						url
					}
					organizers {
						nodes {
							databaseId
						}
					}
					showMap
					showMapLink
					startDate
					startDateUTC
					timezone
					timezoneAbbr
					venue {
						node {
							databaseId
						}
					}
				}
			}
		';
	}

	private function get_expected_response( $event ) : array {
		$cost        = tribe_get_cost( $event->ID );
		$linked_data = tribe( 'tec.json-ld.event' )->get_data( $event->ID )[ $event->ID ];

		return [
			$this->expectedObject(
				'event',
				[
					$this->expectedField(
						'cost',
						$cost ?: null,
					),
					$this->expectedField(
						'costMax',
						! empty( $cost ) ? tribe( 'tec.cost-utils' )->get_maximum_cost( $cost ) : null,
					),
					$this->expectedField(
						'costMin',
						! empty( $cost ) ? tribe( 'tec.cost-utils' )->get_minimum_cost( $cost ) : null,
					),
					$this->expectedField(
						'currencyPosition',
						tribe_get_event_meta( $event->ID, '_EventCurrencyPosition', true ) ?: null,
					),
					$this->expectedField(
						'currencySymbol',
						tribe_get_event_meta( $event->ID, '_EventCurrencySymbol', true ) ?: null,
					),
					$this->expectedField(
						'duration',
						isset( $event->duration ) ? ( $event->duration ?: null ) : null,
					),
					$this->expectedField(
						'endDate',
						tribe_get_end_date( $event->ID, true, DateUtils::DBDATETIMEFORMAT ) ?? null,
					),
					$this->expectedField(
						'endDateUTC',
						tribe_get_event_meta( $event->ID, '_EventEndDateUTC', true ) ?: null,
					),
					$this->expectedField(
						'eventUrl',
						tribe_get_event_meta( $event->ID, '_EventURL', true ) ?: null,
					),
					$this->expectedField(
						'hideFromUpcomming',
						tribe_get_event_meta( $event->ID, '_EventHideFromUpcoming', true ) ?? null,
					),
					$this->expectedField(
						'id',
						! empty( $event->ID ) ? Relay::toGlobalId( 'tribe_events', (string) $event->ID ) : null,
					),
					$this->expectedField(
						'isAllDay',
						tribe_event_is_all_day( $event->ID ),
					),
					$this->expectedField(
						'isFeatured',
						tribe( 'tec.featured_events' )->is_featured( $event->ID ),
					),
					$this->expectedField(
						'isMultiday',
						tribe_event_is_multiday( $event->ID ),
					),
					$this->expectedField(
						'isSticky',
						-1 === $event->menu_order,
					),
					$this->expectedField(
						'origin',
						tribe_get_event_meta( $event->ID, '_EventOrigin', true ) ?: null,
					),
					$this->expectedField(
						'phone',
						tribe_get_event_meta( $event->ID, '_EventPhone', true ) ?: null,
					),
					$this->expectedField(
						'scheduleDetails',
						tribe_events_event_schedule_details( $event->ID, '', '', false ) ?: null,
					),
					$this->expectedField(
						'showMap',
						tribe_get_event_meta( $event->ID, '_EventShowMap', true ) ?? null,
					),
					$this->expectedField(
						'showMapLink',
						tribe_get_event_meta( $event->ID, '_EventShowMapLink', true ) ?? null,
					),
					$this->expectedField(
						'startDate',
						tribe_get_start_date( $event->ID, true, DateUtils::DBDATETIMEFORMAT ) ?? null,
					),
					$this->expectedField(
						'startDateUTC',
						tribe_get_event_meta( $event->ID, '_EventStartDateUTC', true ) ?: null,
					),
					$this->expectedField(
						'timezone',
						Timezones::get_event_timezone_string( $event->ID ) ?: null,
					),
					$this->expectedField(
						'timezoneAbbr',
						tribe_get_event_meta( $event->ID, '_EventTimezoneAbbr', true ) ?: null,
					),
					$this->expectedObject(
						'venue',
						[
							$this->expectedNode(
								'0',
								$this->expectedField(
									'databaseId',
									$this->venue_id
								)
							),
						]
					),
					$this->expectedObject(
						'organizers',
						[
							$this->expectedNode(
								'0',
								$this->expectedField(
									'databaseId',
									$this->organizer_one_id
								)
							),
							$this->expectedNode(
								'1',
								$this->expectedField(
									'databaseId',
									$this->organizer_two_id
								)
							),
						]
					),
					$this->expectedObject(
						'linkedData',
						[
							$this->expectedField(
								'context',
								! empty( $linked_data->{'@context'} ) ? $linked_data->{'@context'} : null,
							),
							$this->expectedField(
								'description',
								! empty( $linked_data->description ) ? wp_strip_all_tags( html_entity_decode( $linked_data->description ) ) : null,
							),
							$this->expectedField(
								'endDate',
								! empty( $linked_data->endDate ) ? $linked_data->endDate : null,
							),
							$this->expectedField(
								'image',
								! empty( $linked_data->image ) ? $linked_data->image : null,
							),
							$this->expectedField(
								'name',
								! empty( $linked_data->name ) ? $linked_data->name : null,
							),
							$this->expectedField(
								'offers',
								! empty( $linked_data->offers ) ? $linked_data->offers : null,
							),
							// Organizer
							$this->expectedField(
								'performer',
								! empty( $linked_data->performer ) ? $linked_data->performer : null,
							),
							$this->expectedField(
								'startDate',
								! empty( $linked_data->startDate ) ? $linked_data->startDate : null,
							),
							$this->expectedField(
								'type',
								! empty( $linked_data->{'@type'} ) ? $linked_data->{'@type'} : null,
							),
							$this->expectedField(
								'url',
								! empty( $linked_data->url ) ? $linked_data->url : null,
							),
							$this->expectedObject(
								'location',
								[
									$this->expectedField(
										'type',
										! empty( $linked_data->location->{'@type'} ) ? $linked_data->location->{'@type'} : null,
									),
								]
							),
							$this->expectedObject(
								'organizer',
								[
									$this->expectedField(
										'type',
										! empty( $linked_data->organizer->{'@type'} ) ? $linked_data->organizer->{'@type'} : null,
									),
								]
							),
							$this->expectedField(
								'offers',
								null
							),
						]
					),
				]
			),
		];
	}
}
