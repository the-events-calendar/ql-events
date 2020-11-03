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
	 */
	public static function register() {
		register_graphql_object_type(
			'OrganizerLinkedData',
			array(
				'description' => __( 'Organizer JSON-LD data', 'ql-events' ),
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
					'telephone'   => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->telephone ) ? $source->telephone : null;
						},
					),
					'email'       => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->email ) ? esc_attr( $source->email ) : null;
						},
					),
					'sameAs'      => array(
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
