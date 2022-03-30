<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\ArrayConfigElementManifestStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class ArrayConfigElementManifestStrategyTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const CURRENT_FILE_NAME = 'tests/_test_files/manifests/array_element/current/RabbitMqConfig.php';

    /**
     * @var string
     */
    protected const ORIGINAL_FILE_NAME = 'tests/_test_files/manifests/array_element/original/RabbitMqConfig.php';

    /**
     * @return void
     */
    public function testStrategy(): void
    {
        $pluginStrategy = new ArrayConfigElementManifestStrategy();
        $result = [];

        $result = $pluginStrategy->generateManifestData(static::CURRENT_FILE_NAME, static::ORIGINAL_FILE_NAME, $result);

        $this->assertEquals([
            'Spryker.TestIntegrator' => [
                ArrayConfigElementManifestStrategy::MANIFEST_KEY => [
                    [
                        'target' => '\\Spryker\\Client\\RabbitMq\\RabbitMqConfig::getTestConfiguration',
                        'value' => '\\Spryker\\Shared\\TestIntegrator\\TestIntegratorAddArrayElementFirst::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST_FIRST',
                    ],
                    [
                        'target' => '\\Spryker\\Client\\RabbitMq\\RabbitMqConfig::getTestConfiguration',
                        'value' => '\\Spryker\\Shared\\TestIntegrator\\TestIntegratorAddArrayElementSecond::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST_SECOND',
                    ],
                ],
            ],
        ], $result);
    }
}
