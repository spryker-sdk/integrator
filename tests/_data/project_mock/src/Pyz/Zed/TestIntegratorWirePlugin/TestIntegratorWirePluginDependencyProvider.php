<?php

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\TestIntegratorWirePluginDependencyProvider as SprykerTestIntegratorWirePluginDependencyProvider;

class TestIntegratorWirePluginDependencyProvider extends SprykerTestIntegratorWirePluginDependencyProvider
{
    public function getTestPlugins() : array
    {
        return [];
    }
}
