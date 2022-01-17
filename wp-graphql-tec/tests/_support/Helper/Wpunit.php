<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
class Wpunit extends \Codeception\Module {

	public function activate_tec() {
		return activate_plugin( 'the-events-calendar/the-events-calendar.php' );
	}

	public function activate_ecp() {
		return activate_plugin( 'events-calendar-pro/events-calendar-pro.php' );
	}

	public function activate_et() {
		return activate_plugin( 'event-tickets/event-tickets.php' );
	}

	public function activate_etp() {
		return activate_plugin( 'event-tickets-plus/event-tickets-plus.php' );
	}

	public function deactivate_tec_plugins() {
		deactivate_plugins(
			[
				'the-events-calendar/the-events-calendar.php',
				'events-calendar-pro/events-calendar-pro.php',
				'event-tickets/event-tickets.php',
				'event-tickets-plus/event-tickets-plus.php',
			]
		);
	}
}
