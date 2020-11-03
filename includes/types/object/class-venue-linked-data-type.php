<?php
/**
 * WPObject Type - VenueLinkedData
 *
 * Resolves venue linked data
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

/**
 * Class Venue_Linked_Data
 */
class Venue_Linked_Data_Type {
	/**
	 * Registers VenueLinkedData type.
	 */
	public static function register() {
		register_graphql_object_type(
			'AddressLinkedData',
			array(
				'description' => __( 'Address JSON-LD data', 'ql-events' ),
				'fields'      => array(
					'type'            => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					),
					'streetAddress'   => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->streetAddress ) ? $source->streetAddress : null;
						},
					),
					'addressLocality' => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->addressLocality ) ? $source->addressLocality : null;
						},
					),
					'addressRegion'   => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->addressRegion ) ? $source->addressRegion : null;
						},
					),
					'postalCode'      => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->postalCode ) ? $source->postalCode : null;
						},
					),
					'addressCountry'  => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->addressCountry ) ? $source->addressCountry : null;
						},
					),
				),
			)
		);

		register_graphql_object_type(
			'VenueLinkedData',
			array(
				'description' => __( 'Venue JSON-LD data', 'ql-events' ),
				'fields'      => array(
					'type'        => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					),
					'name'        => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->name ) ? strip_tags( html_entity_decode( $source->name ) ) : null;
						},
					),
					'description' => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->description ) ? strip_tags( html_entity_decode( $source->description ) ) : null;
						},
					),
					'url'         => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->url ) ? $source->url : null;
						},
					),
					'name'        => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->name ) ? $source->name : null;
						},
					),
					'description' => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->description ) ? $source->description : null;
						},
					),
					'address'     => array(
						'type'    => 'AddressLinkedData',
						'resolve' => function( $source ) {
							return ! empty( $source->address ) ? $source->address : null;
						},
					),
					'telephone'   => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->telephone ) ? $source->telephone : null;
						},
					),
					'sameAs'          => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->sameAs ) ? $source->sameAs : null;
						},
					),
				),
			)
		);
	}
}
