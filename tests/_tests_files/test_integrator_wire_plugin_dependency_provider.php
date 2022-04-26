<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStaticIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStringIndex;
use Spryker\Zed\TestIntegratorWirePlugin\TestIntegratorWirePluginConfig;

class TestIntegratorWirePluginDependencyProvider
{
    public function getTestPlugins(): array
    {
        return [
            new TestIntegratorWirePlugin(),
            TestIntegratorWirePluginConfig::TEST_INTEGRATOR_WIRE_PLUGIN => new TestIntegratorWirePluginIndex(),
            static::TEST_INTEGRATOR_WIRE_PLUGIN_STATIC_INDEX => new TestIntegratorWirePluginStaticIndex(),
            'TEST_INTEGRATOR_WIRE_PLUGIN_STRING_INDEX' => new TestIntegratorWirePluginStringIndex(),
        ];
    }

    public function getTestArrayMergePlugins(): array
    {
        return array_merge([\ArrayObject::class], [
            new TestIntegratorWirePlugin(),
        ], [
            TestIntegratorWirePluginConfig::TEST_INTEGRATOR_WIRE_PLUGIN => new TestIntegratorWirePluginIndex(),
        ], [
            static::TEST_INTEGRATOR_WIRE_PLUGIN_STATIC_INDEX => new TestIntegratorWirePluginStaticIndex(),
        ], [
            'TEST_INTEGRATOR_WIRE_PLUGIN_STRING_INDEX' => new TestIntegratorWirePluginStringIndex(),
        ]);
    }
}
