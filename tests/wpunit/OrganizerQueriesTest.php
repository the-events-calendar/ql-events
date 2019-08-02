<?php

use QL_Events\Test\Factories\Organizer;

class OrganizerQueriesTest extends \Codeception\TestCase\WPTestCase {
    private $admin;
    private $customer;
    private $helper;

    public function setUp() {
        // before
        parent::setUp();

        $this->admin                = $this->factory->user->create( array( 'role' => 'admin' ) );
		$this->customer             = $this->factory->user->create( array( 'role' => 'customer' ) );
        $this->helper               = $this->getModule('\Helper\Wpunit')->organizer();
        $this->factory()->organizer = new Organizer();
    }

    public function tearDown() {
        // your tear down methods here

        // then
        parent::tearDown();
    }

    // tests
    public function testOrganizerQuery() {
        $organizer_id = $this->factory()->organizer->create();

        // Create test query
        $query = '
            query($id: ID!) {
                organizer(id: $id) {
                    email
                    website
                    phone
                }
            }
        ';

        /**
		 * Assertion One
		 */
        $variables = array( 'id' => $this->helper->to_relay_id( $organizer_id ) );
		$actual    = graphql(
            array(
                'query'     => $query,
                'variables' => $variables,
            )
        );
		$expected  = array(
            'data' => array(
                'organizer' => $this->helper->print_query( $organizer_id ),
            ),
        );

		// use --debug flag to view.
		codecept_debug( $actual );

		$this->assertEqualSets( $expected, $actual );
    }

}