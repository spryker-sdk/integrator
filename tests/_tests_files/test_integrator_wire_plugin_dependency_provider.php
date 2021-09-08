<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\TestIntegratorWirePluginDependencyProvider as SprykerTestIntegratorWirePluginDependencyProvider;

class TestIntegratorWirePluginDependencyProvider extends SprykerTestIntegratorWirePluginDependencyProvider
{
    public function getTestPlugins(): array
    {
        return [
            new TestIntegratorWirePlugin(),
        ];
    }
}
