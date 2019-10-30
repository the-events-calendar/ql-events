<?php
/**
 * WPObject Type - Organizer
 *
 * Registers "Organizer" WPObject type and queries
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use Tribe__Events__JSON_LD__Organizer as JSON_LD;
use WPGraphQL\AppContext;

/**
 * Class - Organizer_Type
 */
class Organizer_Type {
	/**
	 * Registers "Organizer" type and queries.
	 */
	public static function register_fields() {
		register_graphql_fields(
			'Organizer',
			array(
				'email'      => array(
					'type'        => 'String',
					'description' => __( 'Organizer email', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_OrganizerEmail', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'website'    => array(
					'type'        => 'String',
					'description' => __( 'Organizer website', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_OrganizerWebsite', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'phone'      => array(
					'type'        => 'String',
					'description' => __( 'Organizer phone number', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_OrganizerPhone', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'linkedData' => array(
					'type'        => 'OrganizerLinkedData',
					'description' => __( 'Organizer JSON-LD object', 'ql-events' ),
					'resolve'     => function( $source ) {
						$instance = JSON_LD::instance();
						$data     = $instance->get_data( $source->ID );
						return ! empty( $data[ $source->ID ] ) ? $data[ $source->ID ] : null;
					},
				),
			)
		);
	}
}
