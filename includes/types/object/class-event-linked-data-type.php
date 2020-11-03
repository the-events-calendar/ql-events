<?php
/**
 * WPObject Type - EventLinkedData
 *
 * Resolves event linked data
 *
 * @package \WPGraphQL\QL_Events\Type\WPObject
 * @since   0.0.1
 */

namespace WPGraphQL\QL_Events\Type\WPObject;

/**
 * Class Event_Linked_Data
 */
class Event_Linked_Data_Type {
	/**
	 * Registers EventLinkedData type.
	 */
	public static function register() {
		register_graphql_object_type(
			'EventLinkedData',
			array(
				'description' => __( 'Event JSON-LD data', 'ql-events' ),
				'fields'      => array(
					'context'     => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->{'@context'} ) ? $source->{'@context'} : null;
						},
					),
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
					'startDate'   => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->startDate ) ? $source->startDate : null;
						},
					),
					'endDate'     => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->endDate ) ? $source->endDate : null;
						},
					),
					'location'    => array(
						'type'    => 'VenueLinkedData',
						'resolve' => function( $source ) {
							return ! empty( $source->location ) ? $source->location : null;
						},
					),
					'organizer'   => array(
						'type'    => 'OrganizerLinkedData',
						'resolve' => function( $source ) {
							return ! empty( $source->organizer ) ? $source->organizer : null;
						},
					),
					'performer'   => array(
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->performer ) ? $source->performer : null;
						},
					),
				),
			)
		);
	}
}
