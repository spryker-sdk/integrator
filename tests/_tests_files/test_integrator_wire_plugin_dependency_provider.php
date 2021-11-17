<?php

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;

class TestIntegratorWirePluginDependencyProvider
{
    public function getTestPlugins() : array
    {
        return [
            new TestIntegratorWirePlugin(),
        ];
    }
}
