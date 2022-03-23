<?php

namespace Pyz\Zed\TestIntegratorDefault;

use Spryker\Zed\TestIntegratorConfigureModule\TestIntegratorConfigureModuleConfig;

class TestIntegratorDefaultConfig
{
    public function getScalarValue()
    {
        return 'scalar_not_the_value_that_we_are_looking_for';
    }

    public function getLiteralValue()
    {
        return 'literal_not_the_value_that_we_are_looking_for';
    }
}
