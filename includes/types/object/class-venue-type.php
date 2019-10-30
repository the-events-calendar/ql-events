<?php
/**
 * WPObject Type - Venue
 *
 * Registers "Venue" WPObject type and queries
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

use Tribe__Events__JSON_LD__Venue as JSON_LD;
use WPGraphQL\AppContext;

/**
 * Class - Venue_Type
 */
class Venue_Type {
	/**
	 * Registers "Venue" type and queries.
	 */
	public static function register_fields() {
		register_graphql_fields(
			'venue',
			array(
				'country'       => array(
					'type'        => 'String',
					'description' => __( 'Venue country', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueCountry', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'address'       => array(
					'type'        => 'String',
					'description' => __( 'Venue address', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueAddress', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'city'          => array(
					'type'        => 'String',
					'description' => __( 'Venue city', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueCity', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'stateProvince' => array(
					'type'        => 'String',
					'description' => __( 'Venue state province', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueStateProvince', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'state'         => array(
					'type'        => 'String',
					'description' => __( 'Venue state', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueState', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'province'      => array(
					'type'        => 'String',
					'description' => __( 'Venue province', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueProvince', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'zip'           => array(
					'type'        => 'String',
					'description' => __( 'Venue zip/postal code', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueZip', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'phone'         => array(
					'type'        => 'String',
					'description' => __( 'Venue phone number', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenuePhone', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'url'           => array(
					'type'        => 'String',
					'description' => __( 'Venue website URL', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueURL', true );
						return ! empty( $value ) ? $value : null;
					},
				),
				'showMap'       => array(
					'type'        => 'Boolean',
					'description' => __( 'Show venue map?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueShowMap', true );
						return ! is_null( $value ) ? $value : false;
					},
				),
				'showMapLink'   => array(
					'type'        => 'Boolean',
					'description' => __( 'Show venue map link?', 'ql-events' ),
					'resolve'     => function( $source ) {
						$value = get_post_meta( $source->ID, '_VenueShowMapLink', true );
						return ! is_null( $value ) ? $value : false;
					},
				),
				'linkedData'    => array(
					'type'        => 'VenueLinkedData',
					'description' => __( 'Venue JSON-LD object', 'ql-events' ),
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
