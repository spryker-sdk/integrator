<?php

namespace Pyz\Client\TestIntegratorAddArrayOption;

use Spryker\Shared\TestIntegrator\TestIntegratorAddArrayOption;
use Spryker\Shared\TestIntegrator\TestIntegratorAddArrayOptionAfter;
use Spryker\Shared\TestIntegrator\TestIntegratorAddArrayOptionBefore;

class TestIntegratorAddArrayOptionConfig
{
    protected function getTestConfiguration(): array
    {
        return [
            TestIntegratorAddArrayOptionBefore::TEST_INTEGRATION_ADD_ARRAY_OPTION_CONST_BEFORE,
            TestIntegratorAddArrayOptionAfter::TEST_INTEGRATION_ADD_ARRAY_OPTION_CONST_AFTER,
            TestIntegratorAddArrayOption::TEST_INTEGRATION_ADD_ARRAY_OPTION_CONST,
        ];
    }
}
