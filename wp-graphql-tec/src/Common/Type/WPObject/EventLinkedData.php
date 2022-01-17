<?php
/**
 * GraphQL Object Type - EventLinkedData
 *
 * @package WPGraphQL\TEC\Common\Type\WPObject * @since 0.0.1
 */

namespace WPGraphQL\TEC\Common\Type\WPObject;

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
					'name'        => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->name ) ? wp_strip_all_tags( html_entity_decode( $source->name ) ) : null;
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
