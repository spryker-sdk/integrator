<?php

use Spryker\Shared\Kernel\KernelConstants;

$config[KernelConstants::PROJECT_NAMESPACES] = [
    'Pyz',
];
$config[KernelConstants::CORE_NAMESPACES] = [
    'SprykerShop',
    'SprykerEco',
    'Spryker',
    'SprykerSdk',
];
$config[\Pyz\Client\TestIntegratorAddConfigArrayElement\TestIntegratorAddConfigArrayElementConfig::TEST_VALUE_CHANGING] = 'Original value';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_VALUE] = 'Value 1';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_VALUE_DEFAULT] = 'Value choice 1';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_VALUE_CHOICE] = 'Value choice 1';

$config[\Spryker\Shared\Kernel\KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED_TRUE] = true;

$config[\Spryker\Shared\Kernel\KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED_FALSE] = false;

$config[\Spryker\Shared\Kernel\KernelConstants::PROJECT_NAMESPACE] = 'Pyz';

$config[\Spryker\Shared\Kernel\KernelConstants::VAR_BOOL_CAST_VALUE] = (bool)$config[\Spryker\Shared\Kernel\KernelConstants::PROJECT_NAMESPACE];

$config[\Spryker\Shared\Kernel\KernelConstants::FUNC_VALUE] = getenv('SOMEKEY');

$config[\Spryker\Shared\Kernel\KernelConstants::FUNC_VALUE2] = (string)getenv('SOMEKEY2');

$config[\Spryker\Shared\Queue\QueueConstants::QUEUE_ADAPTER_CONFIGURATION_DEFAULT] = [
    '\Spryker\Shared\Queue\QueueConfig::CONFIG_QUEUE_ADAPTER' => '\Spryker\Client\RabbitMq\Model\RabbitMqAdapter::class',
    '\Spryker\Shared\Queue\QueueConfig::CONFIG_MAX_WORKER_NUMBER' => 1,
    '\Spryker\Shared\Queue\QueueConfig::CONFIG_FLOAT_VALUE' => 10,
    '\Spryker\Shared\Queue\QueueConfig::CONFIG_BOOL_VALUE' => 1,
];

$config[\Spryker\Shared\Kernel\KernelConstants::COMPLEX_ARRAY_STRUCTURE] = [
    'SprykerShop',
    APPLICATION_SOURCE_DIR . '/vendor/spryker/payment/config/Zed/Oms',
    getenv('SOMEKEY'),
    (bool)$config[\Spryker\Shared\Kernel\KernelConstants::PROJECT_NAMESPACE],
];

$config[\Spryker\Shared\Kernel\KernelConstants::PRIVATE_KEY_PATH] = str_replace('__LINE__', PHP_EOL, getenv('SPRYKER_OAUTH_KEY_PRIVATE') ?: '') ?: null;

$config[\Spryker\Shared\Kernel\KernelConstants::AUTH_DEFAULT_CREDENTIALS] = [
    'yves_system' => [
        'token' => getenv('SPRYKER_ZED_REQUEST_TOKEN') ?: '',
    ],
];

$config[\Spryker\Shared\Kernel\KernelConstants::PRIVATE_NAME] = APPLICATION_SOURCE_DIR . '/Generated/Glue/Specification/spryker_rest_api.schema.yml';

$config[\Spryker\Shared\Oms\OmsConstants::PROCESS_LOCATION] = [
    APPLICATION_SOURCE_DIR . '/vendor/spryker/payment/config/Zed/Oms',
];

$config[\Spryker\Shared\Kernel\KernelConstants::ENCRYPTION_KEY] = getenv('SPRYKER_OAUTH_ENCRYPTION_KEY') ?: null;

$config[\Spryker\Shared\Kernel\KernelConstants::ENCRYPTION_KEY_OTHER] = getenv('SPRYKER_OAUTH_ENCRYPTION_KEY') ? 'test' : 'other_test';

$config[\Spryker\Shared\Kernel\KernelConstants::ACL_DEFAULT_RULES] = [
    [
        'bundle' => 'security-gui',
        'controller' => '*',
        'action' => '*',
        'type' => 'allow',
    ],
    [
        'bundle' => 'acl',
        'controller' => 'index',
        'action' => 'denied',
        'type' => 'allow',
    ],
];

$config[\Spryker\Shared\Kernel\KernelConstants::STORAGE_REDIS_CONNECTION_OPTIONS] = json_decode(getenv('SPRYKER_KEY_VALUE_STORE_CONNECTION_OPTIONS') ?: '[]', true) ?: [];

$config[\Spryker\Shared\Kernel\KernelConstants::LOGGER_CONFIG_GLUE] = \Spryker\Shared\Kernel\KernelConstants::class;

$config[\Spryker\Shared\Kernel\KernelConstants::OAUTH_PROVIDER_NAME] = \Spryker\Zed\OauthAuth0\OauthAuth0Config::PROVIDER_NAME;

$config[\Pyz\Client\TestIntegratorAddConfigArrayElement\TestIntegratorAddConfigArrayElementConfig::TEST_VAR_VALUE] = $config;
