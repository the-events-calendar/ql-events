<?php
/**
 * QL Events  test case
 *
 * For testing the QL Events GraphQL schema.
 *
 * @since 0.8.0
 * @package Tests\WPGraphQL\TestCase
 */
namespace QL_Events\Test\TestCase;

class QLEventsTestCase extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	/**
	 * Creates users and loads factories.
	 */
	public function setUp(): void {
		parent::setUp();

		// Load factories.
		$factories = array(
			'Event',
			'Organizer',
			'RsvpAttendee',
			'Venue',
			'Ticket',
		);

		foreach ( $factories as $factory ) {
			$factory_name                   = strtolower( preg_replace( '/\B([A-Z])/', '_$1', $factory ) );
			$factory_class                  = '\\QL_Events\\Test\\Factories\\' . $factory;
			$this->factory->{$factory_name} = new $factory_class( $this->factory );
		}

		$this->clearSchema();
	}

	public function tearDown(): void {

		// then
		parent::tearDown();
	}

	/**
	 * Logs in as a specific user
	 */
	protected function loginAs( $user_id = 0 ) {
		wp_set_current_user( $user_id );
	}

	/**
	 * Logs out current user.
	 */
	protected function logout() {
		wp_set_current_user( 0 );
	}

	/**
	 * The death of `! empty( $v ) ? apply_filters( $v ) : null;`
	 *
	 * @param array|mixed $possible   Variable whose existence has to be verified, or
	 * an array containing the variable followed by a decorated value to be returned.
	 * @param mixed       $default    Default value to be returned if $possible doesn't exist.
	 *
	 * @return mixed
	 */
	protected function maybe( $possible, $default = self::IS_NULL ) {
		if ( is_array( $possible ) && 2 === count( $possible ) ) {
			list( $possible, $decorated ) = $possible;
		} else {
			$decorated = $possible;
		}
		return ! empty( $possible ) ? $decorated : $default;
	}
}
