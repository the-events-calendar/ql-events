<?php
/**
 * WPInterface Type - Ticket
 *
 * Registers Ticket interface.
 *
 * @package WPGraphQL\QL_Events\Type\WPInterface;
 * @since   0.2.0
 */

namespace WPGraphQL\QL_Events\Type\WPInterface;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\WooCommerce\Data\Factory;
use WP_GraphQL_WooCommerce;

/**
 * Class - Ticket_Interface
 */
class Ticket_Interface {
	/**
	 * Registers the "Ticket" interface.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public static function register_interface() {
		register_graphql_interface_type(
			'Ticket',
			[
				'interfaces'  => [ 'Node' ],
				'description' => __( 'Ticket object', 'ql-events' ),
				'fields'      => self::get_fields(),
				'resolveType' => function ( $value ) {
					$type_registry = \WPGraphQL::get_type_registry();
					switch ( $value->post_type ) {
						case tribe( 'tickets.rsvp' )->ticket_object:
							return $type_registry->get_type( 'RSVPTicket' );
						case tribe( 'tickets.commerce.paypal' )->ticket_object:
							return $type_registry->get_type( 'PayPalTicket' );

						default:
							/**
							 * Filter the Ticket resolve type.
							 *
							 * @param string|null  $type_name  Name of type to be resolved.
							 * @param mixed        $value      Data source.
							 *
							 * @since 0.3.0
							 */
							$type = apply_filters( 'ql_events_resolve_ticket_type', null, $value );
							if ( ! empty( $type ) ) {
								return $type;
							}

							throw new UserError(
								sprintf(
									/* translators: %s: Ticket type */
									__( 'The "%s" ticket type is not supported by the core QL-Events schema.', 'ql-events' ),
									$value->post_type
								)
							);
					}
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'ticket',
			[
				'type'        => 'Ticket',
				'args'        => [
					'id'        => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The globally unique identifier of the object.', 'ql-events' ),
					],
					'idType'    => [
						'type'        => 'RSVPTicketIdType',
						'description' => __( 'Type of unique identifier to fetch by. Default is Global ID', 'ql-events' ),
					],
					'asPreview' => [
						'type'        => 'Boolean',
						'description' => __( 'Whether to return the node as a preview instance', 'ql-events' ),
					],
				],
				'description' => __( 'Query ticket', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					$id_type = isset( $args['idType'] ) ? $args['idType'] : 'global_id';
					$post_id = null;
					switch ( $id_type ) {
						case 'slug':
							return $context->node_resolver->resolve_uri(
								$args['id'],
								[
									'name'      => $args['id'],
									'post_type' => $post_type_object->name,
								]
							);
						case 'uri':
							return $context->node_resolver->resolve_uri(
								$args['id'],
								[
									'post_type' => $post_type_object->name,
									'archive'   => false,
									'nodeType'  => 'Page',
								]
							);
						case 'database_id':
							$post_id = absint( $args['id'] );
							break;
						case 'source_url':
							$url     = $args['id'];
							$post_id = attachment_url_to_postid( $url );
							if ( empty( $post_id ) ) {
								return null;
							}
							$post_id = absint( attachment_url_to_postid( $url ) );
							break;
						case 'global_id':
						default:
							$id_components = Relay::fromGlobalId( $args['id'] );
							if ( ! isset( $id_components['id'] ) || ! absint( $id_components['id'] ) ) {
								throw new UserError( __( 'The ID input is invalid. Make sure you set the proper idType for your input.', 'ql-events' ) );
							}
							$post_id = absint( $id_components['id'] );
							break;
					}

					if ( isset( $args['asPreview'] ) && true === $args['asPreview'] ) {
						$revisions = wp_get_post_revisions(
							$post_id,
							[
								'posts_per_page' => 1,
								'fields'         => 'ids',
								'check_enabled'  => false,
							]
						);
						$post_id   = ! empty( $revisions ) ? array_values( $revisions )[0] : $post_id;
					}

					return absint( $post_id ) ? $context->get_loader( 'post' )->load_deferred( $post_id )->then(
						function ( $post ) use ( $post_type_object ) {
							if ( ! isset( $post->post_type ) || ! in_array(
								$post->post_type,
								[
									'revision',
									$post_type_object->name,
								],
								true
							) ) {
								return null;
							}

							return $post;
						}
					) : null;
				},
			]
		);
	}

	/**
	 * Defines Ticket fields. All child type must have these fields as well.
	 *
	 * @return array
	 */
	public static function get_fields() {
		return [
			'id'         => [
				'type'        => [ 'non_null' => 'ID' ],
				'description' => __( 'Ticket Global ID.', 'ql-events' ),
				'resolve'     => function( $source ) {
					return $source->id;
				},
			],
			'ticketId'   => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'Ticket database ID', 'ql-events' ),
				'resolve'     => function( $source ) {
					return $source->ID;
				},
			],
			'databaseId' => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'Ticket database ID', 'ql-events' ),
				'resolve'     => function( $source ) {
					return $source->ID;
				},
			],
		];
	}
}
