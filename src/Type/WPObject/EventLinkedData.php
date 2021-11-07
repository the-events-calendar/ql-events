<?php
/**
 * GraphQL Object Type - EventLinkedData
 *
 * @package WPGraphQL\TEC\Type\Object
 * @since 0.0.1
 */

namespace WPGraphQL\TEC\Type\WPObject;

/**
 * Class - EventLinkedData
 */
class EventLinkedData {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'EventLinkedData';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			'OffersLinkedData',
			[
				'description' => __( 'Event JSON-LD data', 'wp-graphql-tec' ),
				'fields'      => [
					'availability'  => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->availability ) ? $source->availability : null;
						},
					],
					'category'      => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->category ) ? $source->category : null;
						},
					],
					'price'         => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->price ) ? $source->price : null;
						},
					],
					'priceCurrency' => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->priceCurrency ) ? $source->priceCurrency : null;
						},
					],
					'type'          => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->{'@type'} ) ? $source->{'@type'} : null;
						},
					],
					'url'           => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->url ) ? $source->url : null;
						},
					],
					'validFrom'     => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->validFrom ) ? $source->validFrom : null;
						},
					],
				],
			]
		);

		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'Event JSON-LD data', 'wp-graphql-tec' ),
				'fields'      => [
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
					'endDate'     => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->endDate ) ? $source->endDate : null;
						},
					],
					'image'       => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->image ) ? $source->image : null;
						},
					],
					'location'    => [
						'type'    => 'VenueLinkedData',
						'resolve' => function( $source ) :?object {
							return ! empty( $source->location ) ? $source->location : null;
						},
					],
					'name'        => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->name ) ? wp_strip_all_tags( html_entity_decode( $source->name ) ) : null;
						},
					],
					'offers'      => [
						'type'    => 'OfferslinkedData',
						'resolve' => function( $source ) : ?object {
							return ! empty( $source->offers ) ? $source->offers : null;
						},
					],
					'organizer'   => [
						'type'    => 'OrganizerLinkedData',
						'resolve' => function( $source ) : ?object {
							return ! empty( $source->organizer ) ? $source->organizer : null;
						},
					],
					'performer'   => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->performer ) ? $source->performer : null;
						},
					],
					'startDate'   => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->startDate ) ? $source->startDate : null;
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
