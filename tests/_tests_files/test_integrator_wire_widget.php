<?php

namespace Pyz\Yves\ShopApplication;

use Spryker\Yves\TestIntegratorUnwireWidget\Widget\TestUnwireWidget;
use Spryker\Yves\TestIntegratorWireWidget\Widget\TestWidget;

class ShopApplicationDependencyProvider
{
    /**
     * @return string[]
     */
    protected function getGlobalWidgets() : array
    {
        return [
            TestUnwireWidget::class, TestWidget::class,
        ];
    }
}
