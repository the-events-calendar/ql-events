<?php
/**
 * Disable autoloading while running tests, as the test
 * suite already bootstraps the autoloader and creates
 * fatal errors when the autoloader is loaded twice
 */
define( 'GRAPHQL_DEBUG', true );

if ( ! defined( 'QL_EVENTS_TEST_MODE' ) ) {
	define( 'QL_EVENTS_TEST_MODE', true );
}
