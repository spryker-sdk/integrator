<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorDefault;

use Spryker\Zed\TestIntegratorConfigureModule\TestIntegratorConfigureModuleConfig;

class TestIntegratorDefaultConfig
{
    public function getTestConfiguration(): array
    {
        return [
            TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE,
        ];
    }
}
