<?php
/**
 * Test - Venue Queries
 *
 * @package .
 * @sinse 0.0.1
 */

use GraphQLRelay\Relay;
use WPGraphQL\TEC\Test\TestCase\TecGraphQLTestCase;

class VenueQueriesTest extends TecGraphQLTestCase {
	private int $event_id;
	private int $venue_id;

	/**
	 * {@inheritDoc}
	 */
	public function setUp() : void {
		// before.
		parent::setUp();
		$this->venue_id = $this->factory->venue->create();
		$this->event_id = $this->factory->event->create(
			[
				'venue' => $this->venue_id,
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

		// Then...
		parent::tearDown();
	}

	public function testVenueQueries() : void {
		$venue     = tribe_get_venue_object( $this->venue_id );
		$global_id = Relay::toGlobalId( 'tribe_venue', $this->venue_id );

		$query = $this->get_query();

		$variables = [
			'id'     => $this->venue_id,
			'idType' => 'DATABASE_ID',
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );

		$expected = $this->get_expected_response( $venue );

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

	public function testConnectionArgs() : void {
		$venue_ids = [
			$this->venue_id,
			$this->factory->venue->create( [ 'location' => 'paris' ] ),
			$this->factory->venue->create( [ 'location' => 'new_york' ] ),
		];

		$query = '
			query testConnectionArgs( $where: RootQueryToVenueConnectionWhereArgs ){
				venues( where: $where ) {
					nodes {
						databaseId
					}
				}
			}
		';

		// Test by eventId.
		$variables = [
			'where' => [
				'eventId' => $this->event_id,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $response, '`eventId` has errors' );
		$this->assertCount( 1, $response['data']['venues']['nodes'], '`eventId` does not return correct amount' );
		$this->assertSame( $venue_ids[0], $response['data']['venues']['nodes'][0]['databaseId'], '`eventId` - node is not the same' );

		// Test by hasEvents.
		$variables = [
			'where' => [
				'hasEvents' => true,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $response, '`hasEvents` has errors' );
		$this->assertCount( 1, $response['data']['venues']['nodes'], '`hasEvents` does not return correct amount' );
		$this->assertSame( $venue_ids[0], $response['data']['venues']['nodes'][0]['databaseId'], '`hasEvents` - node is not the same' );

		$variables = [
			'where' => [
				'hasEvents' => false,
			],
		];

		$response = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $response, '`! hasEvents` has errors' );
		$this->assertCount( 2, $response['data']['venues']['nodes'], '`! hasEvents` does not return correct amount' );
		$this->assertSame( $venue_ids[2], $response['data']['venues']['nodes'][0]['databaseId'], '`! hasEvents` - node 0 is not the same' );
		$this->assertSame( $venue_ids[1], $response['data']['venues']['nodes'][1]['databaseId'], '`! hasEvents` - node 1 is not the same' );

		unset( $venue_ids[0] );
		foreach ( $venue_ids as $id ) {
			wp_delete_post( $id );
		}
	}

	private function get_query() : string {
		return '
			query getVenue( $id: ID!, $idType: VenueIdType ) {
				venue( id: $id, idType: $idType ) {
					address
					city
					country
					hasMap
					hasMapLink
					linkedData {
						address {
							addressCountry
							addressLocality
							addressRegion
							postalCode
							streetAddress
							type
						}
						context
						description
						geo {
							latitude
							longitude
							type
						}
						image
						name
						sameAs
						telephone
						type
						url
					}
					mapLink
					phone
					province
					state
					stateProvince
					website
					zip
				}
			}
		';
	}

	private function get_expected_response( $venue ) : array {
		$linked_data = tribe( 'tec.json-ld.venue' )->get_data( $venue->ID )[ $venue->ID ];

		return [
			$this->expectedObject(
				'venue',
				[
					$this->expectedField(
						'address',
						tribe_get_address( $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'city',
						tribe_get_city( $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'country',
						tribe_get_country( $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'mapLink',
						tribe_get_map_link( (string) $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'phone',
						tribe_get_phone( $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'province',
						tribe_get_province( $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField( 'hasMap', (bool) get_post_meta( $venue->ID, '_VenueShowMap', true ) ),
					$this->expectedField( 'hasMapLink', (bool) get_post_meta( $venue->ID, '_VenueShowMapLink', true ) ),
					$this->expectedField(
						'state',
						tribe_get_state( $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'stateProvince',
						tribe_get_stateprovince( $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'website',
						tribe_get_venue_website_url( $venue->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'zip',
						tribe_get_zip( $venue->ID ) ?: static::IS_NULL,
					),
				]
			),
		];
	}
}
