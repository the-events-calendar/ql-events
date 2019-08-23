<?php

class TicketQueriesTest extends \Codeception\TestCase\WPTestCase {
    private $admin;
    private $customer;
    private $helper;

    public function setUp() {
        // before
        parent::setUp();

        $this->admin                = $this->factory->user->create( array( 'role' => 'admin' ) );
		$this->customer             = $this->factory->user->create( array( 'role' => 'customer' ) );
        $this->helper               = $this->getModule('\Helper\Wpunit')->event();
        $this->factory()->event     = new Event();
        $this->factory()->venue     = new Venue();
        $this->factory()->organizer = new Organizer();
    }

    public function tearDown() {
        // your tear down methods here

        // then
        parent::tearDown();
    }

    // tests
    public function testTicketQuery() {
        $organizer_one = $this->factory()->organizer->create();
        $organizer_two = $this->factory()->organizer->create();
        $venue_id      = $this->factory()->venue->create();
        $event_id      = $this->factory()->event->create(
            array(
                'venue' => $venue_id,
                'organizers' => array( $organizer_one, $organizer_two ),
            )
        );
    }

}