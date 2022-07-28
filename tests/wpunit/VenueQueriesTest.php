<?php

use QL_Events\Test\Factories\Venue;

class VenueQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {
    public function expectedVenueData( $id ) {
		$venue = tribe_get_venue_object( $id );

		$expected = array(
			$this->expectedField( 'venue.id', $this->toRelayId( 'post', $id ) ),
			$this->expectedField( 'venue.databaseId', $venue->ID ),
			$this->expectedField( 'venue.address', $venue->address ),
			$this->expectedField( 'venue.country', $venue->country ),
			$this->expectedField( 'venue.city', $venue->city ),
			$this->expectedField( 'venue.stateProvince', $venue->state_province ),
			$this->expectedField( 'venue.state', $venue->state ),
			$this->expectedField( 'venue.province', $venue->province ),
			$this->expectedField( 'venue.zip', $venue->zip ),
		);

		return $expected;
	}

	// tests
    public function testVenueQuery() {
        $venue_id = $this->factory->venue->create();

        // Create test query
        $query = '
            query($id: ID!) {
                venue(id: $id) {
					id
					databaseId
                    country
                    address
                    city
                    stateProvince
                    state
                    province
                    zip
                    phone
                    url
                    showMap
                    showMapLink
                }
            }
        ';

        /**
		 * Assertion One
		 */
        $this->loginAs(1); // Login in as admin since Organizer is not a public post type
        $variables = array( 'id' => $this->toRelayId( 'post', $venue_id ) );
		$response  = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = $this->expectedVenueData( $venue_id );

		$this->assertQuerySuccessful( $response, $expected );
    }

}
