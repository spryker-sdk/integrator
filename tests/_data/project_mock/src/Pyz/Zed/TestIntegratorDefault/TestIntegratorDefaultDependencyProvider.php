<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorDefault;

use Spryker\Zed\TestIntegratorDefault\Communication\Plugin\TestIntegratorDefault1Plugin;
use Spryker\Zed\TestIntegratorDefault\Communication\Plugin\TestIntegratorDefault2Plugin;
use Spryker\Zed\TestIntegratorDefault\TestIntegratorDefaultDependencyProvider as SprykerTestIntegratodDefaultDependencyProvider;
use Spryker\Zed\TestIntegratorUnwirePlugin\Communication\Plugin\TestIntegratorUnwirePlugin;

class TestIntegratorDefaultDependencyProvider extends SprykerTestIntegratodDefaultDependencyProvider
{
    public function getTestPlugins(): array
    {
        return [
            new TestIntegratorDefault1Plugin(),
            new TestIntegratorUnwirePlugin(),
            new TestIntegratorDefault2Plugin(),
        ];
    }
}
