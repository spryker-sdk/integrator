<?php

$config['PROJECT_NAMESPACES'] = [
    'Pyz',
];
$config['CORE_NAMESPACES'] = [
    'SprykerShop',
    'SprykerEco',
    'Spryker',
    'SprykerSdk',
];

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_VALUE] = 'Value 1';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_VALUE_DEFAULT] = 'Value 1';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_VALUE_CHOICE] = 'Value 1';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_COMPLEX_VALUE] = str_replace(
    '__LINE__',
    PHP_EOL,
    getenv('SPRYKER_OAUTH_KEY_PRIVATE') ?: '',
) ?: null;

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_CONCATENATION_VALUE] = APPLICATION_SOURCE_DIR . '/Generated/Glue/Specification/spryker_rest_api.schema.yml';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_CONCATENATION_VALUE] = getenv('SPRYKER_OAUTH_ENCRYPTION_KEY') ?: null;

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

$config[\Spryker\Shared\Kernel\KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED_TRUE] = true;

$config[\Spryker\Shared\Kernel\KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED_FALSE] = false;

$config[\Spryker\Shared\Kernel\KernelConstants::PROJECT_NAMESPACE_TEST] = 'PyzNewTest';
