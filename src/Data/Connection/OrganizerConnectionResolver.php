<?php
/**
 * Resolves connections to Organizers
 *
 * @package WPGraphQL\TEC\Data\Connection
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Data\Connection;

use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use GraphQL\Error\InvariantViolation;
use WPGraphQL\TEC\Model\Organizer;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\Utils\Utils;

/**
 * Class - OrganizerConnectionResolver
 */
class OrganizerConnectionResolver extends PostObjectConnectionResolver {

	/**
	 * {@inheritDoc}
	 */
	public function get_loader_name() {
		return 'tribe_organizer';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvariantViolation
	 */
	public function get_query() {
		$query = tribe_organizers();
		foreach ( $this->query_args as $key => $value ) {
			$query = is_array( $value ) ? $query->where( $key, ...$value ) : $query->where( $key, $value );
		}
		$query = $query->get_query();

		if ( isset( $query->query_vars['suppress_filters'] ) && true === $query->query_vars['suppress_filters'] ) {
			throw new InvariantViolation( __( 'WP_Query has been modified by a plugin or theme to suppress_filters, which will cause issues with WPGraphQL Execution. If you need to suppress filters for a specific reason within GraphQL, consider registering a custom field to the WPGraphQL Schema with a custom resolver.', 'wp-graphql-tec' ) );
		}

		return $query;
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_execute() {
		if ( false === $this->should_execute ) {
			return false;
		}
		/**
		 * For revisions, we only want to execute the connection query if the user
		 * has access to edit the parent post.
		 *
		 * If the user doesn't have permission to edit the parent post, then we shouldn't
		 * even execute the connection
		 */
		if ( isset( $this->post_type ) && 'revision' === $this->post_type ) {
			if ( $this->source instanceof Organizer ) {
				$parent_post_type_obj = get_post_type_object( $this->source->post_type );
				if ( ! isset( $parent_post_type_obj->cap->edit_post ) || ! current_user_can( $parent_post_type_obj->cap->edit_post, $this->source->ID ) ) {
					$this->should_execute = false;
				}
				/**
				 * If the connection is from the RootQuery, check if the user
				 * has the 'edit_posts' capability
				 */
			} else {
				if ( ! current_user_can( 'edit_tribe_organizer' ) ) {
					$this->should_execute = false;
				}
			}
		}

		return $this->should_execute;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_query_args() {
		$query_args = parent::get_query_args();

		// Prepare for later use.
		$first = $this->args['first'] ?? null;
		$last  = $this->args['last'] ?? null;

		$input_fields = $this->sanitize_input_fields( $this->args['where'] ?? [] );

		if ( ! empty( $input_fields ) ) {
			$query_args = array_merge( $query_args, $input_fields );
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

	/**
	 * {@inheritDoc}
	 */
	public function sanitize_input_fields( array $where_args ) {
		$query_args = Utils::map_input(
			$where_args,
			[
				'authorName'    => 'author_name',
				'authorIn'      => 'author__in',
				'authorNotIn'   => 'author__not_in',
				'categoryId'    => 'cat',
				'categoryName'  => 'category_name',
				'categoryIn'    => 'category__in',
				'categoryNotIn' => 'category__not_in',
				'search'        => 's',
				'id'            => 'p',
				'parent'        => 'post_parent',
				'parentIn'      => 'post_parent__in',
				'parentNotIn'   => 'post_parent__not_in',
				'in'            => 'post__in',
				'notIn'         => 'post__not_in',
				'nameIn'        => 'post_name__in',
				'hasPassword'   => 'has_password',
				'password'      => 'post_password',
				'status'        => 'post_status',
				'stati'         => 'post_status',
				'dateQuery'     => 'date_query',
				'hasEvents'     => 'has_events',
				'hasNoEvents'   => 'has_no_events',
			]
		);

		if ( ! empty( $query_args['post_status'] ) ) {
			$allowed_stati             = $this->sanitize_post_stati( $query_args['post_status'] );
			$query_args['post_status'] = ! empty( $allowed_stati ) ? $allowed_stati : [ 'publish' ];
		}

		/**
		 * Filter the input fields
		 * This allows plugins/themes to hook in and alter what $args should be allowed to be passed
		 * from a GraphQL Query to the WP_Query
		 *
		 * @param array              $query_args The mapped query arguments
		 * @param array              $args       Query "where" args
		 * @param mixed              $source     The query results for a query calling this
		 * @param array              $all_args   All of the arguments for the query (not just the "where" args)
		 * @param AppContext         $context    The AppContext object
		 * @param ResolveInfo        $info       The ResolveInfo object
		 * @param mixed|string|array $post_type  The post type for the query
		 *
		 * @return array
		 * @since 0.0.5
		 */
		$query_args = apply_filters( 'graphql_map_input_fields_to_organizer_query', $query_args, $where_args, $this->source, $this->args, $this->context, $this->info, $this->post_type );

		/**
		 * Return the Query Args
		 */
		return ! empty( $query_args ) && is_array( $query_args ) ? $query_args : [];
	}
}
