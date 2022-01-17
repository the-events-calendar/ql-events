<?php
/**
 * Venue helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Events\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Data;

use WPGraphQL\TEC\Abstracts\DataHelper;

/**
 * Class - Event Helper
 */
class VenueHelper extends DataHelper {
	/**
	 * The helper name. E.g. `events` or `tickets`.
	 *
	 * @var string
	 */
	public static string $name = 'venues';

	/**
	 * The GraphQL type. E.g. `Event` or `RsvpTicket`.
	 *
	 * @var string
	 */
	public static string $type = 'Venue';

	/**
	 * The WordPress type. E.g. `tribe_events` or `tec_tc_ticket`.
	 *
	 * @var string
	 */
	public static string $wp_type = 'tribe_venue';

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
		return __NAMESPACE__ . '\\Connection\\VenueConnectionResolver';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function connection_args() : array {
		return [
			'eventId'   => [
				'type'        => 'Int',
				'description' => __( 'Only venues linked to this event post ID.', 'wp-graphql-tec' ),
			],
			'hasEvents' => [
				'type'        => 'Boolean',
				'description' => __( 'Only venues that either have or do not have associated events, based on the provided value.', 'wp-graphql-tec' ),
			],
		];
	}
}
