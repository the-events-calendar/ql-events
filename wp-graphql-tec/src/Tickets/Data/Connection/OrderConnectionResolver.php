<?php
/**
 * Resolves connections to Orders
 *
 * @package WPGraphQL\TEC\Tickets\Data\Connection
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Tickets\Data\Connection;

use GraphQL\Error\InvariantViolation;
use WPGraphQL\TEC\Tickets\Model\Order;
use GraphQL\Type\Definition\ResolveInfo;
use SebastianBergmann\CodeCoverage\Util;
use WPGraphQL\AppContext;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\TEC\Utils\Utils;
use WPGraphQL\Utils\Utils as GraphQLUtils;

/**
 * Class - OrderConnectionResolver
 */
class OrderConnectionResolver extends AbstractConnectionResolver {
	/**
	 * The current post type.
	 *
	 * @var mixed|string|array
	 */
	public $post_type;

	/**
	 * The ORM provider to be used by tribe_tickets().
	 *
	 * @var string
	 */
	public string $orm_provider;

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed|string|array $post_type The post type to resolve for.
	 */
	public function __construct( $source, array $args, AppContext $context, ResolveInfo $info, $post_type = '' ) {
		if ( empty( $post_type ) || 'TicketOrder' === $post_type ) {
			$post_type = array_keys( Utils::get_et_order_types() );
		}

		$this->post_type = $post_type;

		$this->orm_provider = Utils::get_et_provider_for_type( $info->fieldName );

		parent::__construct( $source, $args, $context, $info );
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
			if ( $this->source instanceof Order ) {
				$parent_post_type_obj = get_post_type_object( $this->source->post_type );
				if ( ! isset( $parent_post_type_obj->cap->edit_post ) || ! current_user_can( $parent_post_type_obj->cap->edit_post, $this->source->ID ) ) {
					$this->should_execute = false;
				}
				/**
				 * If the connection is from the RootQuery, check if the user
				 * has the 'edit_posts' capability
				 */
			} else {
				$post_type_obj = get_post_type_object( $this->post_type );
				if (
					! isset( $post_type_obj->cap->edit_post ) ||
					! current_user_can( $post_type_obj->cap->edit_posts )
				) {
					$this->should_execute = false;
				}
			}
		}

		return $this->should_execute;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_loader_name() {
		return 'et_order';
	}

	/**
	 * Return an array of items from the query
	 *
	 * @return array
	 */
	public function get_ids() {
		return $this->query->get_ids();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws InvariantViolation
	 */
	public function get_query() {
		$query = tribe_tickets_orders( $this->orm_provider )->by_args( $this->query_args );

		if ( isset( $query->query_vars['suppress_filters'] ) && true === $query->query_vars['suppress_filters'] ) {
			throw new InvariantViolation( __( 'WP_Query has been modified by a plugin or theme to suppress_filters, which will cause issues with WPGraphQL Execution. If you need to suppress filters for a specific reason within GraphQL, consider registering a custom field to the WPGraphQL Schema with a custom resolver.', 'wp-graphql-tec' ) );
		}

		return $query;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_query_args() {
		/**
		 * Prepare for later use
		 */
		$last  = ! empty( $this->args['last'] ) ? $this->args['last'] : null;
		$first = ! empty( $this->args['first'] ) ? $this->args['first'] : null;

		// Set default query args.
		$query_args = [
			// Ignore sticky posts by default.
			'ignore_sticky_posts' => true,
			// Set the post_type for the query based on the type of post being queried.
			'post_type'           => $this->post_type ?: array_keys( Utils::get_et_order_types() ),
			// This is all we need.
			'fields'              => 'ids',
			// Don't calculate the total rows, it's not needed and can be expensive.
			'no_found_rows'       => true,
			// Set the post_status to "publish" by default.
			// 'post_status'         => 'publish',
			// Set posts_per_page the highest value of $first and $last, with a (filterable) max of 100.
			'posts_per_page'      => min( max( absint( $first ), absint( $last ), 10 ), $this->query_amount ) + 1,
		];

		/**
		 * Set the graphql_cursor_offset which is used by Config::graphql_wp_query_cursor_pagination_support
		 * to filter the WP_Query to support cursor pagination
		 */
		$cursor_offset = $this->get_offset();

		$query_args['graphql_cursor_offset']  = $cursor_offset;
		$query_args['graphql_cursor_compare'] = ( ! empty( $last ) ) ? '>' : '<';

		$query_args['graphql_after_cursor']  = ! empty( $this->get_after_offset() ) ? $this->get_after_offset() : null;
		$query_args['graphql_before_cursor'] = ! empty( $this->get_before_offset() ) ? $this->get_before_offset() : null;

		/**
		 * If the starting offset is not 0 sticky posts will not be queried as the automatic checks in wp-query don't
		 * trigger due to the page parameter not being set in the query_vars, fixes #732
		 */
		if ( 0 !== $cursor_offset ) {
			$query_args['ignore_sticky_posts'] = true;
		}

		/**
		 * Pass the graphql $args to the WP_Query.
		 */
		$query_args['graphql_args'] = $this->args;

		/**
		 * Collect the input_fields and sanitize them to prepare them for sending to the WP_Query
		 */
		$input_fields = [];
		if ( ! empty( $this->args['where'] ) ) {
			$input_fields = $this->sanitize_input_fields( $this->args['where'] );
		}

		/**
		 * Build the query from the where args.
		 */
		if ( ! empty( $this->args['where'] ) ) {
			$query_args = array_merge( $query_args, $input_fields );
			$query      = tribe_tickets_orders( $this->orm_provider )->by_args( $query_args );

			$query_args = $query->build_query()->query_vars;
		}

		/**
		 * If the query is a search, the source is not another Post, and the parent input $arg is not
		 * explicitly set in the query, unset the $query_args['post_parent'] so the search
		 * can search all posts, not just top level posts.
		 */
		if ( ! $this->source instanceof \WP_Post && isset( $query_args['search'] ) && ! isset( $input_fields['parent'] ) ) {
			unset( $query_args['post_parent'] );
		}

		/**
		 * If the query contains search default the results to
		 */
		if ( isset( $query_args['search'] ) && ! empty( $query_args['search'] ) ) {
			/**
			 * Don't order search results by title (causes funky issues with cursors)
			 */
			$query_args['search_orderby_title'] = false;
			$query_args['orderby']              = 'menu_order';
			$query_args['order']                = isset( $last ) ? 'DESC' : 'ASC';
		}

		if ( empty( $this->args['where']['orderby'] ) ) {
			if ( ! empty( $query_args['post__in'] ) ) {
				$post_in = $query_args['post__in'];
				// Make sure the IDs are integers.
				$post_in = array_map( fn( $id ) => absint( $id ), $post_in );

				// If we're coming backwards, let's reverse the IDs.
				if ( ! empty( $this->args['last'] ) || ! empty( $this->args['before'] ) ) {
					$post_in = array_reverse( $post_in );
				}

				if ( ! empty( $this->get_offset() ) ) {
					// Determine if the offset is in the array.
					$key = array_search( $this->get_offset(), $post_in, true );

					// If the offset is in the array.
					if ( false !== $key ) {
						$post_in = array_slice( $post_in, absint( $key ) + 1, null, true );
					}
				}

				$query_args['post__in'] = $post_in;
				$query_args['orderby']  = 'post__in';
				$query_args['order']    = isset( $last ) ? 'DESC' : 'ASC';
			}

			$query_args['orderby'] = 'menu_order';
			$query_args['order']   = isset( $last ) ? 'DESC' : 'ASC';
		}

		/**
		 * Map the orderby inputArgs to the WP_Query
		 */
		if ( isset( $this->args['where']['orderby'] ) && is_array( $this->args['where']['orderby'] ) ) {
			$query_args['orderby'] = [];
			foreach ( $this->args['where']['orderby'] as $orderby_input ) {
				/**
				 * These orderby options should not include the order parameter.
				 */
				if ( in_array(
					$orderby_input['field'],
					[
						'post__in',
						'post_name__in',
						'post_parent__in',
					],
					true
				) ) {
					$query_args['orderby'] = esc_sql( $orderby_input['field'] );
				} elseif ( ! empty( $orderby_input['field'] ) ) {
					$order = $orderby_input['order'];

					if ( isset( $query_args['graphql_args']['last'] ) && ! empty( $query_args['graphql_args']['last'] ) ) {
						if ( 'ASC' === $order ) {
							$order = 'DESC';
						} else {
							$order = 'ASC';
						}
					}

					$query_args['orderby'][ esc_sql( $orderby_input['field'] ) ] = esc_sql( $order );
				}
			}
		}

		/**
		 * Convert meta_value_num to seperate meta_value value field which our
		 * graphql_wp_term_query_cursor_pagination_support knowns how to handle.
		 */
		if ( isset( $query_args['orderby'] ) && 'meta_value_num' === $query_args['orderby'] ) {
			$query_args['orderby'] = [
				'meta_value' => empty( $query_args['order'] ) ? 'DESC' : $query_args['order'],
			];
			unset( $query_args['order'] );
			$query_args['meta_type'] = 'NUMERIC';
		}

		/**
		 * If there's no orderby params in the inputArgs, set order based on the first/last argument.
		 */
		if ( empty( $query_args['orderby'] ) ) {
			$query_args['order_by'] = 'menu_order';
			$query_args['order']    = ! empty( $last ) ? 'DESC' : 'ASC';
		}

		/**
		 * NOTE: Only IDs should be queried here as the Deferred resolution will handle
		 * fetching the full objects, either from cache of from a follow-up query to the DB
		 */
		$query_args['fields'] = 'ids';

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
			'graphql_order_connection_query_args',
			$query_args,
			$this->source,
			$this->args,
			$this->context,
			$this->info
		);
	}

	/**
	 * This sets up the "allowed" args, and translates the GraphQL-friendly keys to WP_Query
	 * friendly keys. There's probably a cleaner/more dynamic way to approach this, but
	 * this was quick. I'd be down to explore more dynamic ways to map this, but for
	 * now this gets the job done.
	 *
	 * @param array $where_args The args passed to the connection.
	 */
	public function sanitize_input_fields( array $where_args ) : array {
		$query_args = GraphQLUtils::map_input(
			$where_args,
			[
				'authorName'         => 'author_name',
				'authorIn'           => 'author__in',
				'authorNotIn'        => 'author__not_in',
				'tagId'              => 'tag_id',
				'tagIds'             => 'tag__and',
				'tagIn'              => 'tag__in',
				'tagNotIn'           => 'tag__not_in',
				'tagSlugAnd'         => 'tag_slug__and',
				'tagSlugIn'          => 'tag_slug__in',
				'search'             => 's',
				'id'                 => 'p',
				'parent'             => 'post_parent',
				'parentIn'           => 'post_parent__in',
				'parentNotIn'        => 'post_parent__not_in',
				'in'                 => 'post__in',
				'notIn'              => 'post__not_in',
				'nameIn'             => 'post_name__in',
				'hasPassword'        => 'has_password',
				'password'           => 'post_password',
				'status'             => 'post_status',
				'stati'              => 'post_status',
				'dateQuery'          => 'date_query',
				'currency'           => 'currency',
				'eventIdIn'          => 'events',
				'eventIdNotIn'       => 'events_not',
				'gateway'            => 'gateway',
				'gatewayOrderId'     => 'gateway_order_id',
				'hash'               => 'hash',
				'purchaserEmail'     => 'purchaser_email',
				'purchaserFirstName' => 'purchaser_first_name',
				'purchaserLastName'  => 'purchaser_last_name',
				'purchaserName'      => 'purchaser_full_name',
				'ticketIdIn'         => 'tickets',
				'ticketIdNotIn'      => 'tickets_not',
			]
		);

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
		$query_args = apply_filters( 'graphql_map_input_fields_to_order_query', $query_args, $where_args, $this->source, $this->args, $this->context, $this->info, $this->post_type );

		/**
		 * Return the Query Args
		 */
		return ! empty( $query_args ) && is_array( $query_args ) ? $query_args : [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_nodes() {
		$nodes = parent::get_nodes();
		if ( ! empty( $nodes ) && ! empty( $this->args['last'] ) ) {
			$nodes = array_reverse( $nodes, true );
		}
		return $nodes;
	}

		/**
		 * Determine whether or not the the offset is valid, i.e the post corresponding to the offset
		 * exists. Offset is equivalent to post_id. So this function is equivalent to checking if the
		 * post with the given ID exists.
		 *
		 * @param int $offset The ID of the node used in the cursor offset.
		 *
		 * @return bool
		 */
	public function is_valid_offset( $offset ) {
		return ! empty( get_post( absint( $offset ) ) );
	}
}
