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
				'venue' => [
					$this->venue_id,
				],
			]
		);

		$this->clearSchema();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown() : void {
		// $this->factory->event->delete( $this->event_id );
		// $this->factory->venue->delete( $this->venue_id );
		// $this->factory->organizer->delete( $this->organizer_one_id );
		// $this->factory->organizer->delete( $this->organizer_two_id );
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
		codecept_debug( $variables );
		codecept_debug( $venue );

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
		codecept_debug( $response );
		$this->assertArrayNotHasKey( 'errors', $response, 'Query by GlobalID has errors' );
		$this->assertQuerySuccessful( $response, $expected );
	}

	public function testVenueQueryArgs() : void {
		$this->markTestIncomplete(
			'This test has not been implemented yet. Requires https://github.com/wp-graphql/wp-graphql/pull/2141.'
		);
	}

	private function get_query() : string {
		return '
			query getVenue( $id: ID!, $idType: VenueIdType ) {
				venue( id: $id, idType: $idType ) {
					address
					city
					country
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
					showMap
					showMapLink
					province
					state
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
						tribe_get_address( $venue->ID ) ?: null,
					),
					$this->expectedField(
						'city',
						tribe_get_city( $venue->ID ) ?: null,
					),
					$this->expectedField(
						'country',
						tribe_get_country( $venue->ID ) ?: null,
					),
					$this->expectedField(
						'mapLink',
						tribe_get_map_link( (string) $venue->ID ) ?: null,
					),
					$this->expectedField(
						'phone',
						tribe_get_phone( $venue->ID ) ?: null,
					),
					$this->expectedField(
						'province',
						tribe_get_province( $venue->ID ) ?: null,
					),
					$this->expectedField(
						'showMap',
						get_post_meta( $venue->ID, '_VenueShowMap', true ) ?? null,
					),
					$this->expectedField(
						'showMapLink',
						get_post_meta( $venue->ID, '_VenueShowMapLink', true ) ?? null,
					),
					$this->expectedField(
						'state',
						tribe_get_state( $venue->ID ) ?: null,
					),
					$this->expectedField(
						'stateProvince',
						tribe_get_stateprovince( $venue->ID ) ?: null,
					),
					$this->expectedField(
						'website',
						tribe_get_venue_website_url( $venue->ID ) ?: null,
					),
					$this->expectedField(
						'zip',
						tribe_get_zip( $venue->ID ) ?: null,
					),
				]
			),
		];
	}
}
