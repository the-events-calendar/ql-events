<?php
/**
 * Test - Organizer Queries
 *
 * @package .
 * @sinse 0.0.1
 */

use GraphQLRelay\Relay;
use WPGraphQL\TEC\Test\TestCase\TecGraphQLTestCase;


class OrganizerQueriesTest extends TecGraphQLTestCase {
	private int $event_id;
	private int $organizer_id;

	/**
	 * {@inheritDoc}
	 */
	public function setUp() : void {
		// before.
		parent::setUp();
		$this->organizer_id = $this->factory->organizer->create();
		$this->event_id     = $this->factory->event->create(
			[
				'organizers' => [
					$this->organizer_id,
				],
			]
		);

		$this->clearSchema();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown() : void {
		wp_delete_post( $this->event_id );
		wp_delete_post( $this->organizer_id );

		// Then...
		parent::tearDown();
	}

	public function testOrganizerQueries() : void {
		$organizer = tribe_get_organizer_object( $this->organizer_id );
		$global_id = Relay::toGlobalId( 'tribe_organizer', $this->organizer_id );

		$query = $this->get_query();

		$variables = [
			'id'     => $this->organizer_id,
			'idType' => 'DATABASE_ID',
		];
		$response  = $this->graphql( compact( 'query', 'variables' ) );

		$expected = $this->get_expected_response( $organizer );

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

		// Test email obsfucation.
		$expected_email = tribe_get_organizer_email( $organizer->ID, false );
		codecept_debug( $response );
		$this->assertSame( $expected_email, html_entity_decode( $response['data']['organizer']['email'] ) );
		$this->assertSame( $expected_email, html_entity_decode( $response['data']['organizer']['linkedData']['email'] ) );
	}

	public function testConnectionArgs() : void {
		$this->markTestIncomplete(
			'This test has not been implemented yet. Requires https://github.com/wp-graphql/wp-graphql/pull/2141.'
		);
	}

	private function get_query() : string {
		return '
			query getOrganizer( $id: ID!, $idType: OrganizerIdType ) {
				organizer( id: $id, idType: $idType ) {
					email(antispambot: true)
					unsanitizedEmail: email(antispambot: false)
					events {
						nodes {
							databaseId
						}
					}
					linkedData {
						context
						description
						email
						image
						name
						sameAs
						type
						telephone
						url
					}
					phone
					website
				}
			}
		';
	}

	private function get_expected_response( $organizer ) : array {
		$linked_data = tribe( 'tec.json-ld.organizer' )->get_data( $organizer->ID )[ $organizer->ID ];

		return [
			$this->expectedObject(
				'organizer',
				[
					// $this->expectedField(
					// 'email',
					// tribe_get_organizer_email( $organizer->ID, true ) ?: static::IS_NULL,
					// ),
					$this->expectedField(
						'unsanitizedEmail',
						tribe_get_organizer_email( $organizer->ID, false ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'phone',
						tribe_get_organizer_phone( $organizer->ID ) ?: static::IS_NULL,
					),
					$this->expectedField(
						'website',
						tribe_get_organizer_website_url( $organizer->ID ) ?: static::IS_NULL,
					),
					$this->expectedObject(
						'linkedData',
						[
							$this->expectedField(
								'context',
								! empty( $linked_data->{'@context'} ) ? $linked_data->{'@context'} : static::IS_NULL,
							),
							$this->expectedField(
								'description',
								! empty( $linked_data->description ) ? wp_strip_all_tags( html_entity_decode( $linked_data->description ) ) : static::IS_NULL,
							),
							// $this->expectedField(
							// 'email',
							// ! empty( $linked_data->email ) ? $linked_data->email : static::IS_NULL,
							// ),
							$this->expectedField(
								'image',
								! empty( $linked_data->image ) ? $linked_data->image : static::IS_NULL,
							),
							$this->expectedField(
								'name',
								! empty( $linked_data->name ) ? $linked_data->name : static::IS_NULL,
							),
							$this->expectedField(
								'sameAs',
								! empty( $linked_data->sameAs ) ? $linked_data->sameAs : static::IS_NULL,
							),
							$this->expectedField(
								'telephone',
								! empty( $linked_data->telephone ) ? $linked_data->telephone : static::IS_NULL,
							),
							$this->expectedField(
								'type',
								! empty( $linked_data->{'@type'} ) ? $linked_data->{'@type'} : static::IS_NULL,
							),
							$this->expectedField(
								'url',
								! empty( $linked_data->url ) ? $linked_data->url : static::IS_NULL,
							),
						]
					),
				]
			),
		];
	}
}
