<?php

use Spryker\Shared\Config\Application\Environment;


define('ROOT', dirname(__DIR__));
define('APPLICATION_ROOT_DIR', dirname(__DIR__));

if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', 'test');
}

if(!defined('APPLICATION_CODE_BUCKET')) {
    define('APPLICATION_CODE_BUCKET', 'SPRYKER_CODE_BUCKET');
}


// Environment::initialize();

require ROOT . '/vendor/autoload.php';
