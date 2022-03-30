<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\ModuleConfigManifestStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class ModuleConfigManifestStrategyTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const CURRENT_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/current/CartPageConfig.php';

    /**
     * @var string
     */
    protected const ORIGINAL_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/original/CartPageConfig.php';

    /**
     * @return void
     */
    public function testStrategy(): void
    {
        $configStrategy = new ModuleConfigManifestStrategy();
        $result = [];

        $result = $configStrategy->generateManifestData(static::CURRENT_FILE_NAME, static::ORIGINAL_FILE_NAME, $result);

        $this->assertEquals([
            'SprykerShop.CartPage' => [
                'configure-module' => [
                    [
                        'target' => '\SprykerShop\Yves\CartPage\CartPageConfig::getConfigMethod',
                        'value' => '\SprykerShop\Yves\CartPage\CartPageConfig::SOME_VALUE',
                    ],
                    [
                        'target' => '\SprykerShop\Yves\CartPage\CartPageConfig::ARRAY_VALUE',
                        'value' => [
                            10,
                            1000,
                        ],
                    ],
                    [
                        'target' => '\SprykerShop\Yves\CartPage\CartPageConfig::ASSOC_ARRAY_VALUE',
                        'value' => [
                            'key_1' => 'key_1_value',
                            'key_2' => 'key_2_value',
                        ],
                    ],
                    [
                        'target' => '\SprykerShop\Yves\CartPage\CartPageConfig::BOOL_VALUE',
                        'value' => true,
                    ],
                ],
            ],
        ], $result);
    }
}
