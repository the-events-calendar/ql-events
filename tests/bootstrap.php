<?php
// This is global bootstrap for autoloading

Codeception\Util\Autoload::addNamespace( 'WPGraphQL\TEC\Test', dirname( __FILE__, 1 ) . '/_support' );
ob_start();
