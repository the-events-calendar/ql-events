<?php
/**
 * GraphQL Object Type - OrganizerLinkedData
 *
 * @package WPGraphQL\TEC\Events\Type\WPObject * @since 0.0.1
 */

namespace WPGraphQL\TEC\Events\Type\WPObject;

/**
 * Class - OrganizerLinkedData
 */
class OrganizerLinkedData {

	/**
	 * Name of the type.
	 *
	 * @var string Type name.
	 */
	public static $type = 'OrganizerLinkedData';
	/**
	 * {@inheritDoc}
	 */
	public static function register_type() : void {
		register_graphql_object_type(
			self::$type,
			[
				'description' => __( 'Organizer JSON-LD data', 'wp-graphql-tec' ),
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
					'email'       => [
						'type'    => 'String',
						'resolve' => function( $source ) : ?string {
							return ! empty( $source->email ) ? $source->email : null;
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
