<?php

namespace Pyz\Zed\TestIntegratorDefault;

use Spryker\Zed\TestIntegratorConfigureModule\TestIntegratorConfigureModuleConfig;
use Spryker\Zed\TestIntegratorDefault\TestIntegratorDefaultConfig as SprykerTestIntegratorDefaultConfig;

class TestIntegratorDefaultConfig extends SprykerTestIntegratorDefaultConfig
{

    public function getTestConfiguration(): array
    {
        return [];
    }
}

