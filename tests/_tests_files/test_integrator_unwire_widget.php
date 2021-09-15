<?php

namespace Pyz\Yves\ShopApplication;

use Spryker\Yves\TestIntegratorUnwireWidget\Widget\TestUnwireWidget;
use Spryker\Yves\TestIntegratorWireWidget\Widget\TestWidget;
use SprykerShop\Yves\ShopApplication\ShopApplicationDependencyProvider as SprykerShopShopApplicationDependencyProvider;

class ShopApplicationDependencyProvider extends SprykerShopShopApplicationDependencyProvider
{
    /**
     * @return string[]
     */
    protected function getGlobalWidgets() : array
    {
        return [
            TestWidget::class,
        ];
    }
}
