<?php
/**
 * Organizer helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Events\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Data;

use WPGraphQL\TEC\Abstracts\DataHelper;

/**
 * Class - Event Helper
 */
class OrganizerHelper extends DataHelper {
	/**
	 * The helper name. E.g. `events` or `tickets`.
	 *
	 * @var string
	 */
	public static string $name = 'organizers';

	/**
	 * The GraphQL type. E.g. `Event` or `RsvpTicket`.
	 *
	 * @var string
	 */
	public static string $type = 'Organizer';

	/**
	 * The WordPress type. E.g. `tribe_events` or `tec_tc_ticket`.
	 *
	 * @var string
	 */
	public static string $wp_type = 'tribe_organizer';

	/**
	 * The name of the DataLoader to use.
	 *
	 * @var string
	 */
	public static string $loader_name = 'post';

	/**
	 * {@inheritDoc}
	 */
	public static function resolver() : string {
		return __NAMESPACE__ . '\\Connection\\OrganizerConnectionResolver';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function connection_args() : array {
		return [
			'eventId'   => [
				'type'        => 'Int',
				'description' => __( 'Only organizers linked to this event post ID.', 'wp-graphql-tec' ),
			],
			'hasEvents' => [
				'type'        => 'Boolean',
				'description' => __( 'Only organizer that have or do not have events, based on the provided value.', 'wp-graphql-tec' ),
			],
		];
	}
}
