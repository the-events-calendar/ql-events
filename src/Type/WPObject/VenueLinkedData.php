<?php
/**
 * GraphQL Object Type - VenueLinkedData
 *
 * @package WPGraphQL\TEC\Type\Object
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Type\WPObject;

/**
 * Class - VenueLinkedData
 */
class VenueLinkedData {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'VenueLinkedData';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			'AddressLinkedData',
			[
				'description' => __( 'Address JSON-LD data', 'wp-graphql-tec' ),
				'fields'      => [
					'addressCountry'  => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->addressCountry ) ? $source->addressCountry : null;
						},
					],
					'addressLocality' => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->addressLocality ) ? $source->addressLocality : null;
						},
					],
					'addressRegion'   => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
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
					'streetAddress'   => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->streetAddress ) ? $source->streetAddress : null;
						},
					],
					'type'            => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					],
				],
			]
		);

		register_graphql_object_type(
			'GeoCoordinatesLinkedData',
			[
				'description' => __( 'GeoCoordinates JSON-LD data', 'wp-graphql-tec' ),
				'fields'      => [
					'latitude'  => [
						'type'    => 'Float',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->latitude ) ? $source->latitude : null;
						},
					],
					'longitude' => [
						'type'    => 'Float',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->longitude ) ? $source->longitude : null;
						},
					],
					'type'      => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					],
				],
			]
		);

		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'Venue JSON-LD data', 'wp-graphql-tec' ),
				'fields'      => [
					'address'     => [
						'type'    => 'AddressLinkedData',
						'resolve' => function( $source ) : ?object {
							return ! empty( $source->address ) ? $source->address : null;
						},
					],
					'context'     => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->{'@context'} ) ? $source->{'@context'} : null;
						},
					],
					'description' => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->description ) ? wp_strip_all_tags( html_entity_decode( $source->description ) ) : null;
						},
					],
					'geo'         => [
						'type'    => 'GeoCoordinatesLinkedData',
						'resolve' => function( $source ) : ?object {
							return ! empty( $source->geo ) ? $source->geo : null;
						},
					],
					'image'       => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->image ) ? $source->image : null;
						},
					],
					'name'        => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->name ) ? wp_strip_all_tags( html_entity_decode( $source->name ) ) : null;
						},
					],
					'sameAs'      => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->sameAs ) ? $source->sameAs : null;
						},
					],
					'telephone'   => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->telephone ) ? $source->telephone : null;
						},
					],
					'type'        => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					],
					'url'         => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->url ) ? $source->url : null;
						},
					],
				],
			]
		);
	}
}
