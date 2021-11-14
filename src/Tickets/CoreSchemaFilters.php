<?php
/**
 * Adds filters that modify core schema.
 *
 * @package \WPGraphQL\TEC\Tickets
 * @since   0.0.1
 */

namespace WPGraphQL\TEC\Tickets;

use WPGraphQL\AppContext;
use WPGraphQL\TEC\Interfaces\Hookable;
use WPGraphQL\TEC\Tickets\Data\Factory;
use WPGraphQL\TEC\Tickets\Data\Loader\TicketLoader;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Hookable {
	/**
	 * {@inheritdoc}
	 */
	public static function register_hooks() : void {
		// Registers post types.
		add_action( 'register_post_type_args', [ __CLASS__, 'register_post_types' ], 10, 2 );

		// Add data-loaders to AppContext.
		add_filter( 'graphql_data_loaders', [ __CLASS__, 'register_data_loaders' ], 10, 2 );

		add_filter( 'graphql_dataloader_pre_get_model', [ Factory::class, 'set_models_for_dataloaders' ], 10, 2 );

		// Add node resolvers.
		add_filter( 'graphql_resolve_node_type', [ Factory::class, 'resolve_node_type' ], 10, 4 );

		// Overwrite default GraphQL configs.
		add_filter( 'graphql_wp_object_type_config', [ Factory::class, 'set_object_type_config' ] );
		add_filter( 'graphql_wp_connection_type_config', [ Factory::class, 'set_connection_type_config' ], );

		// Extend models.
		add_filter( 'graphql_tec_event_model_fields', [ Factory::class, 'add_fields_to_event_model' ], 10, 2 );
	}

	/**
	 * Register TEC post types to GraphQL schema
	 *
	 * @param array  $args      - post-type args.
	 * @param string $post_type - post-type slug.
	 */
	public static function register_post_types( array $args, string $post_type ): array {
		switch ( $post_type ) {
			case 'tec_tc_ticket':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'TcTicket';
				$args['graphql_plural_name'] = 'TcTickets';
				break;
			case 'tribe_rsvp_tickets':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'RsvpTicket';
				$args['graphql_plural_name'] = 'RsvpTickets';
				break;
			case 'tribe_tpp_tickets':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'PayPalTicket';
				$args['graphql_plural_name'] = 'PayPalTickets';
				break;
		}

		return $args;
	}


	/**
	 * Registers data-loaders to be used when resolving TEC-related GraphQL types.
	 *
	 * @param array      $loaders - assigned loaders.
	 * @param AppContext $context - AppContext instance.
	 */
	public static function register_data_loaders( array $loaders, AppContext $context ) : array {
		$ticket_loader     = new TicketLoader( $context );
		$loaders['ticket'] = &$ticket_loader;

		return $loaders;
	}
}
