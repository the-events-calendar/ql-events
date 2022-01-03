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
use WPGraphQL\TEC\Tickets\Data\EventHelper;
use WPGraphQL\TEC\Tickets\Data\Factory;
use WPGraphQL\TEC\Tickets\Data\Loader\AttendeeLoader;
use WPGraphQL\TEC\Tickets\Data\Loader\OrderLoader;
use WPGraphQL\TEC\Tickets\Data\Loader\TicketLoader;
use WPGraphQL\TEC\Tickets\Model\Event;

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
		add_filter( 'graphql_resolve_node_type', [ Factory::class, 'resolve_node_type' ], 10, 2 );

		// Overwrite default GraphQL configs.
		add_filter( 'graphql_wp_object_type_config', [ Factory::class, 'set_object_type_config' ] );
		add_filter( 'graphql_wp_connection_type_config', [ Factory::class, 'set_connection_type_config' ], );

		// Extend models.
		add_filter( 'graphql_tec_event_model_fields', [ Event::class, 'extend' ], 10, 2 );

		add_filter( 'graphql_tec_events_connection_args', [ EventHelper::class, 'add_where_args_to_events_connection' ] );

		add_filter( 'tribe_repository_attendees_query_args', [ Factory::class, 'tribe_fix_orderby_args' ], 10, 3 );
		add_filter( 'tribe_repository_tickets_query_args', [ Factory::class, 'tribe_fix_orderby_args' ], 10, 3 );
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
			case 'tec_tc_attendee':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'TcAttendee';
				$args['graphql_plural_name'] = 'TcAttendees';
				break;
			case 'tribe_rsvp_attendees':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'RsvpAttendee';
				$args['graphql_plural_name'] = 'RsvpAttendees';
				break;
			case 'tribe_tpp_attendees':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'PayPalAttendee';
				$args['graphql_plural_name'] = 'PayPalAttendees';
				break;
			case 'tec_tc_order':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'TcOrder';
				$args['graphql_plural_name'] = 'TcOrders';
				break;
			case 'tec_tpp_orders':
				$args['show_in_graphql']     = true;
				$args['graphql_single_name'] = 'PayPalOrder';
				$args['graphql_plural_name'] = 'PayPalOrders';
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
		$ticket_loader       = new TicketLoader( $context );
		$loaders['ticket']   = &$ticket_loader;
		$attendee_loader     = new AttendeeLoader( $context );
		$loaders['attendee'] = &$attendee_loader;
		$order_loader        = new OrderLoader( $context );
		$loaders['et_order'] = &$order_loader;

		return $loaders;
	}
}
