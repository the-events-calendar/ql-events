<?php
/**
 * WPInterface Type - Attendee
 *
 * Registers Attendee interface.
 *
 * @package WPGraphQL\QL_Events\Type\WPInterface;
 */

namespace WPGraphQL\QL_Events\Type\WPInterface;

use GraphQL\Error\UserError;
use GraphQLRelay\Relay;
use WPGraphQL\AppContext;
use WPGraphQL\Data\DataSource;
use WPGraphQL\Model\Post;
use WPGraphQL\QL_Events\QL_Events;
use WPGraphQL\WooCommerce\Data\Factory;
use TEC\Tickets\Commerce\Attendee;

/**
 * Class - Attendee_Interface
 */
class Attendee_Interface {
	/**
	 * Registers the "Attendee" interface and "attendee" query.
	 *
	 * @param \WPGraphQL\Registry\TypeRegistry $type_registry  Instance of the WPGraphQL TypeRegistry.
	 */
	public static function register_interface( &$type_registry ) {
		register_graphql_interface_type(
			'Attendee',
			[
				'description' => __( 'Attendee object', 'ql-events' ),
				'fields'      => self::get_fields(),
				'resolveType' => function ( $value ) use ( &$type_registry ) {
					$post_type = get_post_type( $value->ID );

					switch ( true ) {
						case tribe( 'tickets.rsvp' )->attendee_object === $post_type:
							return $type_registry->get_type( 'RSVPAttendee' );
						case tribe( 'tickets.commerce.paypal' )->attendee_object === $post_type:
							return $type_registry->get_type( 'PayPalAttendee' );
						case QL_Events::is_ticket_events_plus_loaded() && tribe( 'tickets-plus.commerce.woo' )->attendee_object === $post_type:
							return $type_registry->get_type( 'WooAttendee' );
						default:
							throw new UserError(
								sprintf(
									/* translators: %s: Attendee type */
									__( 'The "%s" attendee type is not supported by the core QL-Events schema.', 'ql-events' ),
									$value->post_type
								)
							);
					}
				},
			]
		);

		register_graphql_field(
			'RootQuery',
			'attendee',
			[
				'type'        => 'Attendee',
				'args'        => [
					'id'        => [
						'type'        => [ 'non_null' => 'ID' ],
						'description' => __( 'The globally unique identifier of the object.', 'ql-events' ),
					],
					'idType'    => [
						'type'        => 'RSVPAttendeeIdType',
						'description' => __( 'Type of unique identifier to fetch by. Default is Global ID', 'ql-events' ),
					],
				],
				'description' => __( 'Query attendee', 'ql-events' ),
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

					return absint( $post_id )
						? $context->get_loader( 'post' )->load_deferred( $post_id )->then(
							function ( $post ) use ( $post_type_object ) {

								// if the post isn't an instance of a Post model, return
								if ( ! $post instanceof Post ) {
									return null;
								}

								return $post;
							}
						)
						: null;
				},
			]
		);
	}

	/**
	 * Defines Attendee fields. All child type must have these fields as well.
	 *
	 * @return array
	 */
	public static function get_fields() {
		$fields = [
			'id'                  => [
				'type'        => [ 'non_null' => 'ID' ],
				'description' => __( 'Attendee Unique ID.', 'ql-events' ),
				'resolve'     => function( $source ) {
					return $source->id;
				},
			],
			'databaseId'          => [
				'type'        => [ 'non_null' => 'Int' ],
				'description' => __( 'Attendee database ID', 'ql-events' ),
				'resolve'     => function( $source ) {
					return $source->ID;
				},
			],
			'ticket'              => [
				'type'        => 'Ticket',
				'description' => __( 'Attendee\'s Ticket', 'ql-events' ),
				'resolve'     => function( $source ) {
					return null;
				},
			],
			'order'               => [
				'type'        => 'TECOrder',
				'description' => __( 'Attendee\'s Order', 'ql-events' ),
				'resolve'     => function( $source ) {
					return null;
				},
			],
			'fullName'            => [
				'type'        => 'String',
				'description' => __( 'Full name of the tickets PayPal "buyer"', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					return null;
				},
			],
			'email'               => [
				'type'        => 'String',
				'description' => __( 'email of the tickets PayPal "buyer"', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					return null;
				},
			],
			'event'               => [
				'type'        => 'Event',
				'description' => __( 'Event attendee is excepted to attend.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					return null;
				},
			],
			'checkedIn'           => [
				'type'        => 'Boolean',
				'description' => __( 'Has attendee checked into the event.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					return null;
				},
			],
			'securityCode'        => [
				'type'        => 'String',
				'description' => __( 'Security code on attendee\'s ticket.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					return null;
				},
			],
			'paidPrice'           => [
				'type'        => 'String',
				'description' => __( 'Security code on attendee\'s ticket.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					return null;
				},
			],
			'priceCurrencySymbol' => [
				'type'        => 'String',
				'description' => __( 'Security code on attendee\'s ticket.', 'ql-events' ),
				'resolve'     => function( $source, array $args, AppContext $context ) {
					return null;
				},
			],
		];

		if ( QL_Events::is_ticket_events_plus_loaded() ) {
			$fields['data'] = [
				'type'        => [ 'list_of' => 'MetaData' ],
				'description' => __( 'Attendee\'s Data', 'ql-events' ),
				'args'        => [
					'key'      => [
						'type'        => 'String',
						'description' => __( 'Retrieve meta by key', 'ql-events' ),
					],
					'keysIn'   => [
						'type'        => [ 'list_of' => 'String' ],
						'description' => __( 'Retrieve multiple metas by key', 'ql-events' ),
					],
					'multiple' => [
						'type'        => 'Boolean',
						'description' => __( 'Retrieve meta with matching keys', 'ql-events' ),
					],
				],
				'resolve'     => function( $source ) {
					$decorator          = tribe( Attendee::class );
					$decorated_attendee = $decorator->get_attendee( get_post( $source->ID ) );

					$meta               = tribe( 'tickets-plus.meta' );
					$attendee_meta_data = $meta->get_attendee_meta_fields( $decorated_attendee->ticket_id, $decorated_attendee->ID );
					if ( isset( $attendee_meta_data[0] ) ) {
						unset( $attendee_meta_data[0] );
					}

					if ( ! is_array( $attendee_meta_data ) ) {
						return [];
					}

					return array_map(
						function( $key, $value ) {
							return (object) compact( 'value', 'key' );
						},
						array_keys( $attendee_meta_data ),
						array_values( $attendee_meta_data )
					);
				},
			];
		}

		return $fields;
	}
}
