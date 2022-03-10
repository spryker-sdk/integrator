<?php

namespace Pyz\Client\TestIntegratorAddConfigArrayElement;

use Spryker\Shared\TestIntegrator\TestIntegratorAddConfigArrayElement;
use Spryker\Shared\TestIntegrator\TestIntegratorAddConfigArrayElementAfter;
use Spryker\Shared\TestIntegrator\TestIntegratorAddConfigArrayElementBefore;

class TestIntegratorAddConfigArrayElementConfig
{
    protected function getTestConfiguration(): array
    {
        return [
            TestIntegratorAddConfigArrayElementBefore::TEST_INTEGRATION_ADD_CONFIG_ARRAY_ELEMENT_CONST_BEFORE,
            TestIntegratorAddConfigArrayElementAfter::TEST_INTEGRATION_ADD_CONFIG_ARRAY_ELEMENT_CONST_AFTER,
            TestIntegratorAddConfigArrayElement::TEST_INTEGRATION_ADD_CONFIG_ARRAY_ELEMENT_CONST,
        ];
    }
}
