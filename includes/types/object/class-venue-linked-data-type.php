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
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_object_type(
			'AddressLinkedData',
			[
				'description' => __( 'Address JSON-LD data', 'ql-events' ),
				'fields'      => [
					'type'            => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					],
					'streetAddress'   => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->streetAddress ) ? $source->streetAddress : null;
						},
					],
					'addressLocality' => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->addressLocality ) ? $source->addressLocality : null;
						},
					],
					'addressRegion'   => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->addressRegion ) ? $source->addressRegion : null;
						},
					],
					'postalCode'      => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->postalCode ) ? $source->postalCode : null;
						},
					],
					'addressCountry'  => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->addressCountry ) ? $source->addressCountry : null;
						},
					],
				],
			]
		);

		register_graphql_object_type(
			'VenueLinkedData',
			[
				'description' => __( 'Venue JSON-LD data', 'ql-events' ),
				'fields'      => [
					'type'        => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					],
					'name'        => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->name ) ? wp_strip_all_tags( html_entity_decode( $source->name ) ) : null;
						},
					],
					'description' => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->description ) ? wp_strip_all_tags( html_entity_decode( $source->description ) ) : null;
						},
					],
					'url'         => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->url ) ? $source->url : null;
						},
					],
					'name'        => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->name ) ? $source->name : null;
						},
					],
					'description' => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->description ) ? $source->description : null;
						},
					],
					'address'     => [
						'type'    => 'AddressLinkedData',
						'resolve' => function( $source ) {
							return ! empty( $source->address ) ? $source->address : null;
						},
					],
					'telephone'   => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->telephone ) ? $source->telephone : null;
						},
					],
					'sameAs'      => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->sameAs ) ? $source->sameAs : null;
						},
					],
				],
			]
		);
	}
}
