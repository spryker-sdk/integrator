<?php

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
