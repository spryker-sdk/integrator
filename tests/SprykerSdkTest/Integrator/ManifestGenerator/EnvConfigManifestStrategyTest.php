<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\EnvConfigManifestStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class EnvConfigManifestStrategyTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const CURRENT_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/current/config.php';

    /**
     * @var string
     */
    protected const ORIGINAL_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/original/config.php';

    /**
     * @return void
     */
    public function testStrategy(): void
    {
        $configStrategy = new EnvConfigManifestStrategy();
        $result = [];

        $result = $configStrategy->generateManifestData(static::CURRENT_FILE_NAME, static::ORIGINAL_FILE_NAME, $result);

        $this->assertEquals([
            'Spryker.Kernel' => [
                'configure-env' => [
                    [
                        'target' => '\Spryker\Shared\Kernel\KernelConstants::RESOLVABLE_CLASS_NAMES_CACHE_ENABLED',
                        'value' => true,
                    ],
                    [
                        'target' => '\Spryker\Shared\Kernel\KernelConstants::PROJECT_NAMESPACE',
                        'value' => 'Pyz',
                    ],
                    [
                        'target' => '\Spryker\Shared\Kernel\KernelConstants::VAR_BOOL_CAST_VALUE',
                        'value' => [
                            'value' => '(bool)$config[\Spryker\Shared\Kernel\KernelConstants::PROJECT_NAMESPACE]',
                            'is_literal' => true,
                        ],
                    ],
                    [
                        'target' => '\Spryker\Shared\Kernel\KernelConstants::FUNC_VALUE',
                        'value' => [
                            'value' => 'getenv(\'SOMEKEY\')',
                            'is_literal' => true,
                        ],
                    ],
                ],
            ],
        ], $result);
    }
}
