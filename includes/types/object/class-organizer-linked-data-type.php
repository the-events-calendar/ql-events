<?php
/**
 * WPObject Type - OrganizerLinkedData
 *
 * Resolves organizer linked data
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

/**
 * Class Organizer_Linked_Data
 */
class Organizer_Linked_Data_Type {
	/**
	 * Registers OrganizerLinkedData type.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_object_type(
			'OrganizerLinkedData',
			[
				'description' => __( 'Organizer JSON-LD data', 'ql-events' ),
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
					'telephone'   => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->telephone ) ? $source->telephone : null;
						},
					],
					'email'       => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->email ) ? esc_attr( $source->email ) : null;
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
