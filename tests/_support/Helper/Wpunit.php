<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
class Wpunit extends \Codeception\Module {
	/**
	 * HOOK:
	 * triggered after module is created and configuration is loaded
	 */
	public function _initialize() {
		// Helper classes
		require_once __DIR__ . '/tec-helpers/class-wcg-helper.php';
		require_once __DIR__ . '/tec-helpers/class-event-helper.php';
		require_once __DIR__ . '/tec-helpers/class-organizer-helper.php';
		require_once __DIR__ . '/tec-helpers/class-venue-helper.php';
	}

	public function event() {
		return \Event_Helper::instance();
	}

	public function organizer() {
		return \Organizer_Helper::instance();
	}

	public function venue() {
		return \Venue_Helper::instance();
	}

	public function clear_loader_cache( $loader_name ) {
		$loader = \WPGraphQL::get_app_context()->getLoader( $loader_name );
		$loader->clearAll();
	}
}
