<?php

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStaticIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStringIndex;
use Spryker\Zed\TestIntegratorWirePlugin\TestIntegratorWirePluginConfig;

class TestIntegratorWirePluginDependencyProvider
{
    public function getTestPlugins() : array
    {
        return [
            new TestIntegratorWirePlugin(),
            TestIntegratorWirePluginConfig::TEST_INTEGRATOR_WIRE_PLUGIN => new TestIntegratorWirePluginIndex(),
            static::TEST_INTEGRATOR_WIRE_PLUGIN_STATIC_INDEX => new TestIntegratorWirePluginStaticIndex(),
            'TEST_INTEGRATOR_WIRE_PLUGIN_STRING_INDEX' => new TestIntegratorWirePluginStringIndex(),
        ];
    }
}
