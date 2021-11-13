<?php
/**
 * Factory Class
 *
 * This class serves as a factory for all ET resolvers.
 *
 * @package WPGraphQL\TEC\Tickets\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data;

use GraphQL\Deferred;
use WPGraphQL\AppContext;
use WPGraphQL\TEC\Utils\Utils;
use WPGraphQL\TEC\Tickets\Type\WPInterface;
use WPGraphQL\TEC\Traits\PostTypeResolverMethod;

/**
 * Class - Factory
 */
class Factory {
	use PostTypeResolverMethod;

	/**
	 * Returns a ticket object.
	 *
	 * @param int        $id      Group ID.
	 * @param AppContext $context AppContext object.
	 * @return Deferred|null
	 */
	public static function resolve_ticket_object( $id, AppContext $context ) :?Deferred {
		if ( empty( $id ) ) {
			return null;
		}

		$context->get_loader( 'ticket' )->buffer( [ $id ] );

		return new Deferred(
			function () use ( $id, $context ) {
				return $context->get_loader( 'ticket' )->load( $id );
			}
		);
	}

	/**
	 * Overwrites the GraphQL config for auto-registered object types.
	 *
	 * @param array $config .
	 */
	public static function set_object_type_config( array $config ) : array {
		$post_type = Utils::graphql_type_to_post_type( $config['name'] );

		if ( is_null( $post_type ) ) {
			return $config;
		}

		switch ( $post_type ) {
			case 'tribe_rsvp_tickets':
				$config['interfaces'] = array_merge(
					$config['interfaces'],
					[
						WPInterface\Ticket::$type,
					]
				);
				break;
			case 'tec_tc_ticket':
			case 'tribe_tpp_tickets':
				$config['interfaces'] = array_merge( $config['interfaces'], [ WPInterface\PurchasableTicket::$type ] );
				break;
		}

		return $config;
	}

}
