<?php
define('TEST_INTEGRATOR_MODE' , 'true');

define('ROOT', dirname(__DIR__));
define('APPLICATION_ROOT_DIR', dirname(__DIR__).DIRECTORY_SEPARATOR.'tests/tmp');

if (!defined('APPLICATION_ENV')) {
    // define('APPLICATION_ENV', 'test');
}

if(!defined('APPLICATION_CODE_BUCKET')) {
    // define('APPLICATION_CODE_BUCKET', 'SPRYKER_CODE_BUCKET');
}

if(!defined('APPLICATION_VENDOR_DIR')) {
    define('APPLICATION_VENDOR_DIR', 'tests/project/vendor');
}


require ROOT . '/vendor/autoload.php';
