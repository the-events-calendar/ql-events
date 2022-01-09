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
use WPGraphQL\Type\WPEnumType;

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

	public function testPagination() : void {
		$event_ids = [
			$this->event_id,
			$this->factory->event->create( [ 'when' => '+48 hours' ] ),
			$this->factory->event->create( [ 'when' => '+72 hours' ] ),
			$this->factory->event->create( [ 'when' => '+96 hours' ] ),
			$this->factory->event->create( [ 'when' => '+120 hours' ] ),
			$this->factory->event->create( [ 'when' => '+144 hours' ] ),
		];

		$event_ids = array_reverse( $event_ids );

		$query = '
			query testPagination( $first: Int, $after: String, $last:Int, $before: String ) {
				events( first: $first, after: $after, last: $last, before: $before ) {
					nodes {
						databaseId
					}
					pageInfo{
						hasNextPage
						hasPreviousPage
						startCursor
						endCursor
					}
					edges {
						cursor
					}
				}
			}
		';

		$variables = [
			'first'  => 2,
			'after'  => null,
			'last'   => null,
			'before' => null,
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );

		// Check `first` argument.

		$this->assertArrayNotHasKey( 'errors', $response, '`first` has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], 'First does not return correct amount.' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][0]['databaseId'], 'First - node 0 is not same.' );
		$this->assertSame( $event_ids[1], $response['data']['events']['nodes'][1]['databaseId'], 'First - node 1 is not same.' );
		$this->assertTrue( $response['data']['events']['pageInfo']['hasNextPage'], 'First does not have next page.' );
		$this->assertFalse( $response['data']['events']['pageInfo']['hasPreviousPage'], 'First has previous page.' );

		// Check `after` argument.
		$variables = [
			'first'  => 2,
			'after'  => $response['data']['events']['pageInfo']['endCursor'],
			'last'   => null,
			'before' => null,
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $response, 'first/after #1 has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], 'First/after #1 does not return correct amount.' );
		$this->assertSame( $event_ids[2], $response['data']['events']['nodes'][0]['databaseId'], 'First/after #1 - node 0 is not same.' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][1]['databaseId'], 'First/after #1 - node 1 is not same.' );
		$this->assertTrue( $response['data']['events']['pageInfo']['hasNextPage'], 'First/after #1 does not have next page.' );
		$this->assertTrue( $response['data']['events']['pageInfo']['hasPreviousPage'], 'First/after #1 does not have previous page.' );

		$variables = [
			'first'  => 2,
			'after'  => $response['data']['events']['pageInfo']['endCursor'],
			'last'   => null,
			'before' => null,
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $response, 'first/after #2 has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], 'First/after #2 does not return correct amount.' );
		$this->assertSame( $event_ids[4], $response['data']['events']['nodes'][0]['databaseId'], 'First/after #2 - node 0 is not same.' );
		$this->assertSame( $event_ids[5], $response['data']['events']['nodes'][1]['databaseId'], 'First/after #2 - node 1 is not same.' );
		$this->assertFalse( $response['data']['events']['pageInfo']['hasNextPage'], 'First/after #2 has next page.' );
		$this->assertTrue( $response['data']['events']['pageInfo']['hasPreviousPage'], 'First/after #2 does not have previous page.' );

		// Check last argument.
		$variables = [
			'first'  => null,
			'after'  => null,
			'last'   => 2,
			'before' => null,
		];
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $response, 'Last has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], 'Last does not return correct amount.' );
		$this->assertSame( $event_ids[5], $response['data']['events']['nodes'][0]['databaseId'], 'Last - node 0 is not same.' );
		$this->assertSame( $event_ids[4], $response['data']['events']['nodes'][1]['databaseId'], 'Last - node 1 is not same.' );
		$this->assertFalse( $response['data']['events']['pageInfo']['hasNextPage'], 'Last has next page.' );
		$this->assertTrue( $response['data']['events']['pageInfo']['hasPreviousPage'], 'Last does not have previous page.' );

		// Check `before` argument.
		$variables = [
			'first'  => null,
			'after'  => null,
			'last'   => 2,
			'before' => $response['data']['events']['pageInfo']['endCursor'],
		];
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $response, 'Last/before #1 has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], 'Last/before #1 does not return correct amount.' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], 'Last/before #1 - node 0 is not same.' );
		$this->assertSame( $event_ids[2], $response['data']['events']['nodes'][1]['databaseId'], 'Last/before #1 - node 1 is not same.' );
		$this->assertTrue( $response['data']['events']['pageInfo']['hasNextPage'], 'Last/before #1 does not have next page.' );
		$this->assertTrue( $response['data']['events']['pageInfo']['hasPreviousPage'], 'Last/before #1 does not have previous page.' );

		$variables = [
			'first'  => null,
			'after'  => null,
			'last'   => 2,
			'before' => $response['data']['events']['pageInfo']['endCursor'],
		];
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $response, 'Last/before #2 has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], 'Last/before #2 does not return correct amount.' );
		$this->assertSame( $event_ids[1], $response['data']['events']['nodes'][0]['databaseId'], 'Last/before #2 - node 0 is not same.' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][1]['databaseId'], 'Last/before #2 - node 1 is not same.' );
		$this->assertTrue( $response['data']['events']['pageInfo']['hasNextPage'], 'Last/before #2 does not have next page.' );
		$this->assertFalse( $response['data']['events']['pageInfo']['hasPreviousPage'], 'Last/before #2 has previous page.' );

		unset( $event_ids[5] );
		foreach ( $event_ids as $id ) {
			wp_delete_post( $id );
		}
	}

	public function testConnectionArgs() : void {
		wp_set_current_user( $this->admin->ID );
		$cat1      = $this->factory()->term->create( [ 'taxonomy' => 'tribe_events_cat' ] );
		$event_ids = [
			$this->event_id, // 0
			$this->factory->event->create( // 1
				[
					'cost'       => 0,
					'when'       => '+3 days 9:00',
					'meta_input' => [
						'_EventAllDay'    => true,
						'_tribe_featured' => true,
					],
				]
			),
			$this->factory->event->create( // 2
				[
					'cost'       => 20,
					'when'       => '-10 days 9:00',
					'duration'   => '127800',
					'meta_input' => [
						'_EventHideFromUpcoming' => true,
					],
					'menu_order' => -1,
				]
			),
			$this->factory->event->create( // 3
				[
					'organizers' => $this->organizer_two_id,
					'timezone'   => 'Europe/Moscow',
					'currency'   => 'EUR',
					'tax_input'  => [
						'tribe_events_cat' => [ $cat1 ],
					],
				],
			),
		];

		$query = '
			query testConnectionArgs( $where: RootQueryToEventConnectionWhereArgs ) {
				events( where: $where ) {
					nodes {
						cost
						databaseId
						endDate
						isAllDay
						isFeatured
						isHiddenFromUpcoming
						isMultiday
						isSticky
						startDate
						timezone
						eventCategories {
							nodes{
								databaseId
							}
						}
						organizerDatabaseIds
						venueDatabaseId
					}
				}
			}
		';

		// @todo: Test by cost.

		// Test `endsAfter`.
		$variables = [
			'where' => [
				'endsAfter' => [
					'dateTime' => '+2 days',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`endsAfter` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`endsAfter` does not return correct amount' );
		$this->assertSame( $event_ids[1], $response['data']['events']['nodes'][0]['databaseId'], '`endsAfter` - node is not the same' );

		// Test `endsBefore`.
		$variables = [
			'where' => [
				'endsBefore' => [
					'dateTime' => 'today',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`endsBefore` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`endsBefore` does not return correct amount' );
		$this->assertSame( $event_ids[2], $response['data']['events']['nodes'][0]['databaseId'], '`endsBefore` - node is not the same' );

		// Test `endsBetween`.
		$variables = [
			'where' => [
				'endsBetween' => [
					'startDateTime' => '+24 hours 9:00',
					'endDateTime'   => '+24 hours 12:00',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`endsBetween` has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], '`endsBetween` does not return correct amount' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], '`endsBetween` - node 0 is not the same' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][1]['databaseId'], '`endsBetween` - node 1 is not the same' );

		// Test `endsOnOrBefore`.
		$variables = [
			'where' => [
				'endsOnOrBefore' => [
					'dateTime' => '+26 hours 11:00',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`endsOnOrBefore` has errors' );
		$this->assertCount( 3, $response['data']['events']['nodes'], '`endsOnOrBefore` does not return correct amount' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], '`endsOnOrBefore` - node 0 is not the same' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][1]['databaseId'], '`endsOnOrBefore` - node 1 is not the same' );
		$this->assertSame( $event_ids[2], $response['data']['events']['nodes'][2]['databaseId'], '`endsOnOrBefore` - node 2 is not the same' );

		// Test `eventDateOverlaps`.
		$variables = [
			'where' => [
				'eventDateOverlaps' => [
					'startDateTime' => '+24 hours 9:00',
					'endDateTime'   => '+48 hours',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`eventDateOverlaps` has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], '`eventDateOverlaps` does not return correct amount' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], '`eventDateOverlaps` - node 0 is not the same' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][1]['databaseId'], '`eventDateOverlaps` - node is not the same' );

		// Test `isAllDay`.
		$variables = [
			'where' => [
				'isAllDay' => true,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`isAllDay` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`isAllDay` does not return correct amount' );
		$this->assertSame( $event_ids[1], $response['data']['events']['nodes'][0]['databaseId'], '`isAllDay` - node 0 is not the same' );

		// Test `isFeatured`.
		$variables = [
			'where' => [
				'isFeatured' => true,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`isFeatured` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`isFeatured` does not return correct amount' );
		$this->assertSame( $event_ids[1], $response['data']['events']['nodes'][0]['databaseId'], '`isFeatured` - node 0 is not the same' );

		// Test `isHidden`.
		$variables = [
			'where' => [
				'isHidden' => true,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`isHidden` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`isHidden` does not return correct amount' );
		$this->assertSame( $event_ids[2], $response['data']['events']['nodes'][0]['databaseId'], '`isHidden` - node 0 is not the same' );

		// Test `isMultiday`.
		$variables = [
			'where' => [
				'isMultiday' => true,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`isMultiday` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`isMultiday` does not return correct amount' );
		$this->assertSame( $event_ids[2], $response['data']['events']['nodes'][0]['databaseId'], '`isMultiday` - node 0 is not the same' );

		// Test `isSticky`.
		$variables = [
			'where' => [
				'isSticky' => true,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`isSticky` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`isSticky` does not return correct amount' );
		$this->assertSame( $event_ids[2], $response['data']['events']['nodes'][0]['databaseId'], '`isSticky` - node 0 is not the same' );

		// Test `runsBetween`.
		$variables = [
			'where' => [
				'runsBetween' => [
					'startDateTime' => 'today',
					'endDateTime'   => '+2 days',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`runsBetween` has errors' );
		$this->assertCount( 2, $response['data']['events']['nodes'], '`runsBetween` does not return correct amount' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], '`runsBetween` - node 0 is not the same' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][1]['databaseId'], '`runsBetween` - node 1 is not the same' );

		// Test `startsAfter`.
		$variables = [
			'where' => [
				'startsAfter' => [
					'dateTime' => '+2 days',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`startsAfter` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`startsAfter` does not return correct amount' );
		$this->assertSame( $event_ids[1], $response['data']['events']['nodes'][0]['databaseId'], '`startsAfter` - node 0 is not the same' );

		// Test `startsBefore`.
		$variables = [
			'where' => [
				'startsBefore' => [
					'dateTime' => '-8 days',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`startsAfter` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`startsAfter` does not return correct amount' );
		$this->assertSame( $event_ids[2], $response['data']['events']['nodes'][0]['databaseId'], '`startsAfter` - node 0 is not the same' );

		// Test `startsOnDate`.
		$variables = [
			'where' => [
				'startsOnDate' => [
					'dateTime' => '+3 days 9:00',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`startsOnDate` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`startsOnDate` does not return correct amount' );
		$this->assertSame( $event_ids[1], $response['data']['events']['nodes'][0]['databaseId'], '`startsOnDate` - node 0 is not the same' );

		// Test `startsOnOrAfter`.
		$variables = [
			'where' => [
				'startsOnOrAfter' => [
					'dateTime' => '+2 days',
				],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`startsOnOrAfter` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`startsOnOrAfter` does not return correct amount' );
		$this->assertSame( $event_ids[1], $response['data']['events']['nodes'][0]['databaseId'], '`startsOnOrAfter` - node 0 is not the same' );

		// Test `timezone`.
		$variables = [
			'where' => [
				'timezone' => 'Europe/Moscow',
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`timezone` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`timezone` does not return correct amount' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], '`timezone` - node 0 is not the same' );

		// Test `categoryId`.
		$variables = [
			'where' => [
				'categoryId' => $cat1,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`categoryId` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`categoryId` does not return correct amount' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], '`categoryId` - node 0 is not the same' );

		// Test `categoryIn`.
		$variables = [
			'where' => [
				'categoryIn' => [ $cat1 ],
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`categoryIn` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`categoryIn` does not return correct amount' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], '`categoryIn` - node 0 is not the same' );

		// Test `categoryName`.
		$variables = [
			'where' => [
				'categoryName' => get_term( $cat1, 'tribe_events_cat' )->name,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`categoryName` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`categoryName` does not return correct amount' );
		$this->assertSame( $event_ids[3], $response['data']['events']['nodes'][0]['databaseId'], '`categoryName` - node 0 is not the same' );

		// Test `categoryNotIn`.
		$variables = [
			'where' => [
				'categoryNotIn' => $cat1,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`categoryNotIn` has errors' );
		$this->assertCount( 3, $response['data']['events']['nodes'], '`categoryNotIn` does not return correct amount' );

		// Test `organizerId`.
		$variables = [
			'where' => [
				'organizerId' => $this->organizer_one_id,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`organizerId` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`organizerId` does not return correct amount' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][0]['databaseId'], '`organizerId` - node 0 is not the same' );

		// Test `organizerIn`.
		$variables = [
			'where' => [
				'organizerIn' => $this->organizer_one_id,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`organizerIn` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`organizerIn` does not return correct amount' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][0]['databaseId'], '`organizerIn` - node 0 is not the same' );

		// Test `organizerName`.
		$variables = [
			'where' => [
				'organizerName' => tribe_get_organizer( $this->organizer_one_id ),
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`organizerName` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`organizerName` does not return correct amount' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][0]['databaseId'], '`organizerName` - node 0 is not the same' );

		// Test `venueId`.
		$variables = [
			'where' => [
				'venueId' => $this->venue_id,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`venueId` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`venueId` does not return correct amount' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][0]['databaseId'], '`venueId` - node 0 is not the same' );

		// Test `venueIn`.
		$variables = [
			'where' => [
				'venueIn' => $this->venue_id,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`venueIn` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`venueIn` does not return correct amount' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][0]['databaseId'], '`venueIn` - node 0 is not the same' );

		// Test `venueName`.
		$variables = [
			'where' => [
				'venueName' => tribe_get_venue( $this->venue_id ),
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $response, '`venueName` has errors' );
		$this->assertCount( 1, $response['data']['events']['nodes'], '`venueName` does not return correct amount' );
		$this->assertSame( $event_ids[0], $response['data']['events']['nodes'][0]['databaseId'], '`venueName` - node 0 is not the same' );

		unset( $event_ids[0] );
		foreach ( $event_ids as $id ) {
			wp_delete_post( $id );
		}
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
					hasMap
					hasMapLink
					id
					isAllDay
					isHiddenFromUpcoming
					isFeatured
					isMultiday
					isPast
					isSticky
					linkedData {
						context
						description
						endDate
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
					organizerDatabaseIds
					organizerIds
					organizers {
						nodes {
							databaseId
						}
					}
					origin
					scheduleDetails
					scheduleDetailsShort
					startDate
					startDateUTC
					timezone
					timezoneAbbr
					venueDatabaseId
					venueId
					venue {
						databaseId
					}
				}
			}
		';
	}

	private function get_expected_response( $event ) : array {
		$cost        = tribe_get_formatted_cost( $event->ID );
		$linked_data = tribe( 'tec.json-ld.event' )->get_data( $event->ID )[ $event->ID ];

		return [
			$this->expectedObject(
				'event',
				[
					$this->expectedField(
						'cost',
						$cost ?: static::IS_NULL,
					),
					$this->expectedField(
						'costMax',
						! empty( $cost ) ? tribe( 'tec.cost-utils' )->get_maximum_cost( $cost ) : static::IS_NULL,
					),
					$this->expectedField(
						'costMin',
						! empty( $cost ) ? tribe( 'tec.cost-utils' )->get_minimum_cost( $cost ) : static::IS_NULL,
					),
					$this->expectedField(
						'currencyPosition',
						WPEnumType::get_safe_name( tribe_get_event_meta( $event->ID, '_EventCurrencyPosition', true ) ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'currencySymbol',
						tribe_get_event_meta( $event->ID, '_EventCurrencySymbol', true ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'duration',
						(int) tribe_get_event_meta( $event->ID, '_EventDuration', true ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'endDate',
						tribe_get_end_date( $event->ID, true, DateUtils::DBDATETIMEFORMAT ) ?? static::IS_NULL,
					),
					$this->expectedField(
						'endDateUTC',
						tribe_get_event_meta( $event->ID, '_EventEndDateUTC', true ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'eventUrl',
						tribe_get_event_meta( $event->ID, '_EventURL', true ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'hasMap',
						! empty( tribe_get_event_meta( $event->ID, '_EventShowMap', true ) ),
					),
					$this->expectedField(
						'hasMapLink',
						! empty( tribe_get_event_meta( $event->ID, '_EventShowMapLink', true ) ),
					),
					$this->expectedField(
						'id',
						Relay::toGlobalId( 'tribe_events', (string) $event->ID )
					),
					$this->expectedField(
						'isAllDay',
						tribe_event_is_all_day( $event->ID ),
					),
					$this->expectedField(
						'isHiddenFromUpcoming',
						! empty( tribe_get_event_meta( $event->ID, '_EventHideFromUpcoming', true ) ),
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
						tribe_get_event_meta( $event->ID, '_EventOrigin', true ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'scheduleDetails',
						tribe_events_event_schedule_details( $event->ID, '', '', false ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'scheduleDetailsShort',
						tribe_events_event_short_schedule_details( $event->ID, '', '', false ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'startDate',
						tribe_get_start_date( $event->ID, true, DateUtils::DBDATETIMEFORMAT ) ?? static::IS_NULL,
					),
					$this->expectedField(
						'startDateUTC',
						tribe_get_event_meta( $event->ID, '_EventStartDateUTC', true ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'timezone',
						Timezones::get_event_timezone_string( $event->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'timezoneAbbr',
						tribe_get_event_meta( $event->ID, '_EventTimezoneAbbr', true ) ?: static::IS_NULL,
					),
					$this->expectedObject(
						'venue',
						[
							$this->expectedField(
								'databaseId',
								$this->venue_id
							),
						]
					),
					$this->expectedObject(
						'organizers',
						[
							$this->expectedNode(
								'nodes',
								[
									$this->expectedField(
										'databaseId',
										$this->organizer_one_id
									),
								],
								0
							),
							$this->expectedNode(
								'nodes',
								[
									$this->expectedField(
										'databaseId',
										$this->organizer_two_id
									),
								],
								1
							),
						]
					),
					$this->expectedObject(
						'linkedData',
						[
							$this->expectedField(
								'context',
								! empty( $linked_data->{'@context'} ) ? $linked_data->{'@context'} : static::IS_NULL,
							),
							$this->expectedField(
								'description',
								! empty( $linked_data->description ) ? wp_strip_all_tags( html_entity_decode( $linked_data->description ) ) : static::IS_NULL,
							),
							$this->expectedField(
								'endDate',
								! empty( $linked_data->endDate ) ? $linked_data->endDate : static::IS_NULL,
							),
							$this->expectedField(
								'image',
								! empty( $linked_data->image ) ? $linked_data->image : static::IS_NULL,
							),
							$this->expectedObject(
								'location',
								[
									$this->expectedField(
										'type',
										! empty( $linked_data->location->{'@type'} ) ? $linked_data->location->{'@type'} : static::IS_NULL,
									),
								]
							),
							$this->expectedField(
								'name',
								! empty( $linked_data->name ) ? $linked_data->name : static::IS_NULL,
							),
							$this->expectedNode(
								'offers',
								[
									$this->expectedField( 'type', ! empty( $linked_data->offers->{'@type'} ) ? $linked_data->offers->{'@type'} : static::IS_NULL ),
								],
								0
							),
							$this->expectedObject(
								'organizer',
								[
									$this->expectedField(
										'type',
										! empty( $linked_data->organizer->{'@type'} ) ? $linked_data->organizer->{'@type'} : static::IS_NULL,
									),
								]
							),
							$this->expectedField(
								'performer',
								! empty( $linked_data->performer ) ? $linked_data->performer : static::IS_NULL,
							),
							$this->expectedField(
								'startDate',
								! empty( $linked_data->startDate ) ? $linked_data->startDate : static::IS_NULL,
							),
							$this->expectedField(
								'type',
								! empty( $linked_data->{'@type'} ) ? $linked_data->{'@type'} : static::IS_NULL,
							),
							$this->expectedField(
								'url',
								! empty( $linked_data->url ) ? $linked_data->url : static::IS_NULL,
							),
						]
					),
				]
			),
		];
	}
}
