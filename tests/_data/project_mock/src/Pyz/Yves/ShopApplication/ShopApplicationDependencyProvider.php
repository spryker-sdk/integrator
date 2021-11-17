<?php

namespace Pyz\Yves\ShopApplication;

use Spryker\Yves\TestIntegratorUnwireWidget\Widget\TestUnwireWidget;

class ShopApplicationDependencyProvider
{
    /**
     * @return string[]
     */
    protected function getGlobalWidgets() : array
    {
        return [
            TestUnwireWidget::class,
        ];
    }
}
