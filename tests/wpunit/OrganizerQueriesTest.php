<?php

class OrganizerQueriesTest extends \QL_Events\Test\TestCase\QLEventsTestCase {
	public function expectedOrganizerData( $id ) {
		$organizer = tribe_get_organizer_object( $id );

		$expected = array(
			$this->expectedField( 'organizer.id', $this->toRelayId( 'post', $id ) ),
			$this->expectedField( 'organizer.databaseId', $organizer->ID ),
			$this->expectedField( 'organizer.phone', $organizer->phone ),
			$this->expectedField( 'organizer.website', $organizer->website ),
			$this->expectedField( 'organizer.email', $organizer->email ),
		);

		return $expected;
	}

	// tests
    public function testOrganizerQuery() {
        $organizer_id = $this->factory->organizer->create();

        // Create test query
        $query = '
            query($id: ID!) {
                organizer(id: $id) {
					id
					databaseId
                    email
                    website
                    phone
                }
            }
        ';

        /**
		 * Assertion One
		 */
		$this->loginAs(1); // Login in as admin since Organizer is not a public post type
        $variables = array( 'id' => $this->toRelayId( 'post', $organizer_id ) );
		$response = $this->graphql( compact( 'query', 'variables' ) );
		$expected  = $this->expectedOrganizerData( $organizer_id );

		$this->assertQuerySuccessful( $response, $expected );
    }

}
