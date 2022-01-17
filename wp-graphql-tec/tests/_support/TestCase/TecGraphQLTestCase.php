<?php
/**
 * WPGraphQL test case
 *
 * For testing WPGraphQL responses.
 *
 * @since 0.0.1
 * @package WPGraphQL\TEC\Test\TestCase
 */

namespace WPGraphQL\TEC\Test\TestCase;

/**
 * Class - GraphQLTestCase
 */
class TecGraphQLTestCase extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	/**
	 * Holds the User ID of a user whith the "admin" role. For use through the tests for the purpose of testing user access levels.
	 *
	 * @var WP_User
	 */
	protected $admin;

	/**
	 * Creates users and loads factories.
	 */
	public function setUp() : void {
		parent::setUp();

		// Load factories.
		$factories = [
			'Event',
			'Organizer',
			'Venue',
		];

		foreach ( $factories as $factory ) {
			$factory_name                   = strtolower( preg_replace( '/\B([A-Z])/', '_$1', $factory ) );
			$factory_class                  = '\\WPGraphQL\\TEC\\Test\\Factories\\' . $factory;
			$this->factory->{$factory_name} = new $factory_class( $this->factory );
		}

		$this->admin = $this->factory()->user->create_and_get( [ 'role' => 'administrator' ] );
	}

	/**
	 * Post test tear down.
	 */
	public function tearDown(): void {
		// Your tear down methods here.
		wp_delete_user( $this->admin->id );

		// Then...
		parent::tearDown();
	}

	/**
	 * Programmatically generate an expectedField array for assertions.
	 *
	 * @param array $value_array .
	 * @return array
	 */
	protected function get_expected_fields( $value_array ): array {
		$expected = [];
		foreach ( $value_array as $key => $value ) {
			$expected[] = $this->expectedField( $key, $value );
		}
		return $expected;
	}
}
