<?php
define('TEST_INTEGRATOR_MODE', 'true');
define('TMP_DIRECTORY_NAME', 'tmp');
define('DATA_DIRECTORY_NAME', '_data');
define('ROOT_TESTS', __DIR__);

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

if (!defined('APPLICATION_ROOT_DIR')) {
    define('APPLICATION_ROOT_DIR', ROOT_TESTS . DIRECTORY_SEPARATOR . TMP_DIRECTORY_NAME);
}

if (!defined('APPLICATION_VENDOR_DIR')) {
    define('APPLICATION_VENDOR_DIR', APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor');
}

require ROOT . '/vendor/autoload.php';
