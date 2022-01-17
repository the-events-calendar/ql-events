<?php

namespace WPGraphQL\TEC\Test\Factories;

use Tribe__Events__Main as Main;

class Organizer extends \WP_UnitTest_Factory_For_Post {

	function create_object( $args ) {
		$args['post_type'] = Main::ORGANIZER_POST_TYPE;

		$title    = 'Organizer' . uniqid();
		$lc_title = strtolower( $title );

		$defaults = [
			'meta_input' => [
				'_OrganizerPhone'   => '+1-234-567-8910',
				'_OrganizerWebsite' => 'http://example.com',
				'_OrganizerEmail'   => 'contact@example.com',
			],
		];

		if ( isset( $args['meta_input'] ) ) {
			$defaults['meta_input'] = array_merge( $defaults['meta_input'], $args ['meta_input'] );
			unset( $args['meta_input'] );
		}

		return parent::create_object( array_merge( $defaults, $args ) );
	}
}
