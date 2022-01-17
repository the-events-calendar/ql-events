<?php
/**
 * Resolves connections to Organizers
 *
 * @package WPGraphQL\TEC\Events\Data\Connection
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Data\Connection;

use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;

/**
 * Class - OrganizerConnectionResolver
 */
class OrganizerConnectionResolver extends PostObjectConnectionResolver {
	/**
	 * The current post type.
	 *
	 * @var mixed|string|array
	 */
	public $post_type;

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed|string|array $post_type The post type to resolve for.
	 */
	public function __construct( $source, array $args, AppContext $context, ResolveInfo $info, $post_type = 'tribe_organizer' ) {

		/**
		 * The $post_type can either be a single value or an array of post_types to
		 * pass to WP_Query.
		 *
		 * If the value is revision or attachment, we will leave the value
		 * as a string, as we validate against this later.
		 *
		 * If the value is anything else, we cast as an array. For example
		 *
		 * $post_type = 'post' would become [ 'post ' ], as we check later
		 * for `in_array()` if the $post_type is not "attachment" or "revision"
		 */
		if ( 'revision' === $post_type || 'attachment' === $post_type ) {
			$this->post_type = $post_type;
		} else {
			$post_type = is_array( $post_type ) ? $post_type : [ $post_type ];
			unset( $post_type['attachment'] );
			unset( $post_type['revision'] );
			$this->post_type = $post_type;
		}

		parent::__construct( $source, $args, $context, $info, $post_type );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_ids() {
		return $this->query->get_posts() ?: [];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvariantViolation when suppress_filters is enabled.
	 */
	public function get_query() {
		$query = tribe_organizers()->by_args( $this->query_args )->build_query();

		if ( isset( $query->query_vars['suppress_filters'] ) && true === $query->query_vars['suppress_filters'] ) {
			throw new InvariantViolation( __( 'WP_Query has been modified by a plugin or theme to suppress_filters, which will cause issues with WPGraphQL Execution. If you need to suppress filters for a specific reason within GraphQL, consider registering a custom field to the WPGraphQL Schema with a custom resolver.', 'wp-graphql-tec' ) );
		}

		return $query;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_query_args() {
		$query_args = parent::get_query_args();

		// Event ID.
		if ( isset( $this->args['where']['eventId'] ) ) {
			$query_args['event'] = $this->args['where']['eventId'];
			unset( $this->args['where']['eventId'] );
		}
		// Only with events.
		// Only with or without events.
		if ( isset( $this->args['where']['hasEvents'] ) ) {
			if ( $this->args['where']['hasEvents'] ) {
				$query_args['has_events'] = true;
			}
			if ( false === $this->args['where']['hasEvents'] ) {
				$query_args['has_no_events'] = true;
			}
			unset( $this->args['where']['hasEvents'] );
		}

		/**
		 * Filter the query_args that should be applied to the query. This filter is applied AFTER the input args from
		 * the GraphQL Query have been applied and has the potential to override the GraphQL Query Input Args.
		 *
		 * @param array       $query_args array of query_args being passed to the
		 * @param mixed       $source     Source passed down from the resolve tree
		 * @param array       $args       array of arguments input in the field as part of the GraphQL query
		 * @param AppContext  $context    object passed down zthe resolve tree
		 * @param ResolveInfo $info       info about fields passed down the resolve tree
		 */
		return apply_filters(
			'graphql_tec_organizer_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}
}
