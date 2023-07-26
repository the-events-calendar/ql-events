<?php
/**
 * Enum Type - Events_Virtual_Show_Embed_At_Enum
 *
 * @package WPGraphQL\WooCommerce\Pro\Type\WPEnum
 * @since   0.3.0
 */

namespace WPGraphQL\QL_Events\Type\WPEnum;

/**
 * Class Events_Virtual_Show_Embed_At_Enum
 */
class Events_Virtual_Show_Embed_At_Enum {
	/**
	 * Registers type
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_enum_type(
			'EventsVirtualShowEmbedAtEnum',
			[
				'description' => __( 'Triggers for displaying virtual events content.', 'ql-events' ),
				'values'      => [
					'ON_EVENT_PUBLISH' => [ 'value' => 'immediately' ],
					'ON_EVENT_START'   => [ 'value' => 'at-start' ],
				],
			]
		);
	}
}
