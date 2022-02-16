<?php

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\TestIntegratorWirePluginConfig;

class TestIntegratorWirePluginDependencyProvider
{
    public function getTestPlugins() : array
    {
        return [
            TestIntegratorWirePluginConfig::TEST_INTEGRATOR_WIRE_PLUGIN => new TestIntegratorWirePlugin(),
        ];
    }
}
