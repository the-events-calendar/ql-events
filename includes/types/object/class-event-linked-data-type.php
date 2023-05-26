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
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function register() {
		register_graphql_object_type(
			'EventLinkedData',
			[
				'description' => __( 'Event JSON-LD data', 'ql-events' ),
				'fields'      => [
					'context'     => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->{'@context'} ) ? $source->{'@context'} : null;
						},
					],
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
					'startDate'   => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->startDate ) ? $source->startDate : null;
						},
					],
					'endDate'     => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							//@codingStandardsIgnoreLine.
							return ! empty( $source->endDate ) ? $source->endDate : null;
						},
					],
					'location'    => [
						'type'    => 'VenueLinkedData',
						'resolve' => function( $source ) {
							return ! empty( $source->location ) ? $source->location : null;
						},
					],
					'organizer'   => [
						'type'    => 'OrganizerLinkedData',
						'resolve' => function( $source ) {
							return ! empty( $source->organizer ) ? $source->organizer : null;
						},
					],
					'performer'   => [
						'type'    => 'String',
						'resolve' => function( $source ) {
							return ! empty( $source->performer ) ? $source->performer : null;
						},
					],
				],
			]
		);
	}
}
