<?php
// This is global bootstrap for autoloading
Codeception\Util\Autoload::addNamespace( 'QL_Events\Test', __DIR__ . '/_support' );

codecept_debug( 'HERE:::::::::::');

require_once( __DIR__ . '/_support/Factories/Utils.php' );
