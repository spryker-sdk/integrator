<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\PluginsManifestStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class PluginsManifestStrategyTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const CURRENT_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/current/ApplicationDependencyProvider.php';

    /**
     * @var string
     */
    protected const ORIGINAL_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/original/ApplicationDependencyProvider.php';

    /**
     * @return void
     */
    public function testStrategy(): void
    {
        $pluginStrategy = new PluginsManifestStrategy();
        $result = [];

        $result = $pluginStrategy->generateManifestData(static::CURRENT_FILE_NAME, static::ORIGINAL_FILE_NAME, $result);

        $this->assertEquals([
            'Spryker.Session' => [
                'wire-plugin' => [
                    [
                        'target' => '\Spryker\Zed\Application\ApplicationDependencyProvider::getBackofficeApplicationPlugins',
                        'source' => '\Spryker\Zed\Session\Communication\Plugin\Application\SessionApplicationPlugin',
                    ],
                    [
                        'target' => '\Spryker\Zed\Application\ApplicationDependencyProvider::getBackofficeApplicationPluginsWithRegularReturn',
                        'source' => '\Spryker\Zed\Session\Communication\Plugin\Application\SessionApplicationPlugin',
                    ],
                ],
            ],
            'Spryker.EventDispatcher' => [
                'unwire-plugin' => [
                    [
                        'target' => '\Spryker\Zed\Application\ApplicationDependencyProvider::getBackofficeApplicationPlugins',
                        'source' => '\Spryker\Zed\EventDispatcher\Communication\Plugin\Application\EventDispatcherApplicationPlugin',
                    ],
                    [
                        'target' => '\Spryker\Zed\Application\ApplicationDependencyProvider::getBackofficeApplicationPluginsWithRegularReturn',
                        'source' => '\Spryker\Zed\EventDispatcher\Communication\Plugin\Application\EventDispatcherApplicationPlugin',
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @return void
     */
    public function testStrategyWithNotExistingFile(): void
    {
        $pluginStrategy = new PluginsManifestStrategy();
        $result = [];

        $result = $pluginStrategy->generateManifestData(static::CURRENT_FILE_NAME, 'file_not_exists.php', $result);

        $this->assertEquals([
            'Spryker.Session' => [
                'wire-plugin' => [
                    [
                        'target' => '\Spryker\Zed\Application\ApplicationDependencyProvider::getBackofficeApplicationPlugins',
                        'source' => '\Spryker\Zed\Session\Communication\Plugin\Application\SessionApplicationPlugin',
                    ],
                    [
                        'target' => '\Spryker\Zed\Application\ApplicationDependencyProvider::getBackofficeApplicationPluginsWithRegularReturn',
                        'source' => '\Spryker\Zed\Session\Communication\Plugin\Application\SessionApplicationPlugin',
                    ],
                ],
            ],
        ], $result);
    }
}
