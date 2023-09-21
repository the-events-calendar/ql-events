<?php

// Ensure the CT1 code branch is enabled.
use TEC\Common\Monolog\Logger;
use TEC\Events\Custom_Tables\V1\Activation;

// This is global bootstrap for autoloading
Codeception\Util\Autoload::addNamespace( 'QL_Events\Test', __DIR__ . '/_support' );

require_once __DIR__ . '/_support/Factories/Utils.php';

// Disable the CT1 implementation by default; it can be loaded later in the suite configuration files.
putenv( 'TEC_CUSTOM_TABLES_V1_DISABLED=1' );
$_ENV['TEC_CUSTOM_TABLES_V1_DISABLED'] = 1;
