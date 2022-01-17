<?php
/**
 * Order Helper methods for the resolver Factory.
 *
 * @package WPGraphQL\TEC\Tickets\Data
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data;

use WPGraphQL\TEC\Abstracts\DataHelper;
use WPGraphQL\TEC\Tickets\Type\Enum\CurrencyCodeEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\PaymentGatewaysEnum;
use WPGraphQL\TEC\Tickets\Type\Enum\TicketTypeEnum;
use WPGraphQL\TEC\Utils\Utils;

/**
 * Class - Order Helper
 */
class OrderHelper extends DataHelper {
	/**
	 * The helper name. E.g. `events` or `tickets`.
	 *
	 * @var string
	 */
	public static string $name = 'Orders';

	/**
	 * The GraphQL type. E.g. `Event` or `RsvpTicket`.
	 *
	 * @var string
	 */
	public static string $type = 'TicketOrder';

	/**
	 * The WordPress type. E.g. `tribe_events` or `tec_tc_ticket`.
	 *
	 * @var string
	 */
	public static string $wp_type = 'TicketOrder';

	/**
	 * The name of the DataLoader to use.
	 *
	 * @var string
	 */
	public static string $loader_name = 'et_order';

	/**
	 * {@inheritDoc}
	 */
	public static function resolver() : string {
		return __NAMESPACE__ . '\\Connection\\OrderConnectionResolver';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function connection_args() : array {
		return [
			'currency'           => [
				'type'        => CurrencyCodeEnum::$type,
				'description' => __( 'Filters Orders that use the specified currency ISO code.', 'wp-graphql-tec' ),
			],
			'eventIdIn'          => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters Orders attached to a specific event ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'eventIdNotIn'       => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters Orders not attached to a specific event ID or array of IDs.', 'wp-graphql-tec' ),
			],
			/** phpcs:disable
			 * @todo figure out how to add with ETP, since they dont use gateways.
			 *
			'gateway'            => [
				'type'        => PaymentGatewaysEnum::$type,
				'description' => __( 'Filters Orders that match the specified payment gateway.', 'wp-graphql-tec' ),
			],
			'gatewayOrderId'     => [
				'type'        => 'String',
				'description' => __( 'Filters Orders that match the specified payment gateway order id.', 'wp-graphql-tec' ),
			],
			 * phpcs:enable
			 */
			'hash'               => [
				'type'        => 'ID',
				'description' => __( 'Filters Orders that match the specified hash key.', 'wp-graphql-tec' ),
			],
			'purchaserEmail'     => [
				'type'        => 'String',
				'description' => __( 'Filters Orders that match the specified purchaser email.', 'wp-graphql-tec' ),
			],
			'purchaserFirstName' => [
				'type'        => 'String',
				'description' => __( 'Filters Orders that match the specified purchaser first name.', 'wp-graphql-tec' ),
			],
			'purchaserLastName'  => [
				'type'        => 'String',
				'description' => __( 'Filters Orders that match the specified purchaser last name.', 'wp-graphql-tec' ),
			],
			'purchaserName'      => [
				'type'        => 'String',
				'description' => __( 'Filters Orders that match the specified purchaser name.', 'wp-graphql-tec' ),
			],
			'ticketIdIn'         => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters Orders attached to a specific ticket ID or array of IDs.', 'wp-graphql-tec' ),
			],
			'ticketIdNotIn'      => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'Filters Orders not attached to a specific ticket ID or array of IDs.', 'wp-graphql-tec' ),
			],
		];
	}
}
