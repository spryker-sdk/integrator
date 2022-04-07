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

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_VALUE_DEFAULT] = 'Value choice 1';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_VALUE_CHOICE] = 'Value choice 1';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_COMPLEX_VALUE] = str_replace(
    '__LINE__',
    PHP_EOL,
    getenv('SPRYKER_OAUTH_KEY_PRIVATE') ?: '',
) ?: null;

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_CONCATENATION_VALUE] = APPLICATION_SOURCE_DIR . '/Generated/Glue/Specification/spryker_rest_api.schema.yml';

$config[\Spryker\Shared\TestIntegratorConfigureEnv\TestIntegratorConfigureEnvConstants::TEST_CONCATENATION_VALUE] = getenv('SPRYKER_OAUTH_ENCRYPTION_KEY') ?: null;
