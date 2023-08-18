<?php
define('TEST_INTEGRATOR_MODE', 'true');
define('TMP_DIRECTORY_NAME', 'tmp');
define('ROOT_TESTS', __DIR__);

defined('ROOT')
    || define('ROOT', dirname(__DIR__));

defined('INTEGRATOR_ROOT_DIR')
|| define('INTEGRATOR_ROOT_DIR', dirname(__DIR__));

defined('DATA_PROVIDER_DIR')
    || define('DATA_PROVIDER_DIR', ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'spryker-sdk' . DIRECTORY_SEPARATOR . 'manifest-test-data-provider');

defined('APPLICATION_ROOT_DIR')
    || define('APPLICATION_ROOT_DIR', ROOT_TESTS . DIRECTORY_SEPARATOR . TMP_DIRECTORY_NAME);

defined('APPLICATION_VENDOR_DIR')
    || define('APPLICATION_VENDOR_DIR', APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor');

defined('APPLICATION_SOURCE_DIR')
    || define('APPLICATION_SOURCE_DIR', APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'src');

defined('APPLICATION_STANDALONE_MODULES_DIR')
    || define('APPLICATION_STANDALONE_MODULES_DIR', APPLICATION_VENDOR_DIR . DIRECTORY_SEPARATOR . 'spryker');

require ROOT . '/vendor/autoload.php';
