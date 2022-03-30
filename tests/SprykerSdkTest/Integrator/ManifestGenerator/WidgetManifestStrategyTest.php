<?php

namespace SprykerSdkTest\Integrator\ManifestGenerator;

use SprykerSdk\Integrator\ManifestGenerator\WidgetsManifestStrategy;
use SprykerSdkTest\Integrator\BaseTestCase;

class WidgetManifestStrategyTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const CURRENT_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/current/ShopApplicationDependencyProvider.php';

    /**
     * @var string
     */
    protected const ORIGINAL_FILE_NAME = 'tests/_test_files/manifests/dependency_provider/original/ShopApplicationDependencyProvider.php';

    /**
     * @return void
     */
    public function testStrategy(): void
    {
        $widgetStrategy = new WidgetsManifestStrategy();
        $result = [];

        $result = $widgetStrategy->generateManifestData(static::CURRENT_FILE_NAME, static::ORIGINAL_FILE_NAME, $result);

        $this->assertEquals([
            'SprykerShop.MultiCartWidget' => [
                'wire-widget' => [
                    [
                        'source' => '\SprykerShop\Yves\MultiCartWidget\Widget\AddToMultiCartWidget',
                    ],
                ],
            ],
            'SprykerShop.ShoppingListWidget' => [
                'unwire-widget' => [
                    [
                        'source' => '\SprykerShop\Yves\ShoppingListWidget\Widget\AddToShoppingListWidget',
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @return void
     */
    public function testStrategyWithNotExistedOriginalFle(): void
    {
        $widgetStrategy = new WidgetsManifestStrategy();
        $result = [];

        $result = $widgetStrategy->generateManifestData(static::CURRENT_FILE_NAME, 'not_existed_file.php', $result);

        $this->assertEquals([
            'SprykerShop.MultiCartWidget' => [
                'wire-widget' => [
                    [
                        'source' => '\SprykerShop\Yves\MultiCartWidget\Widget\AddToMultiCartWidget',
                    ],
                ],
            ],
        ], $result);
    }
}
