<?php

namespace Pyz\Client\TestIntegratorAddArrayElement;

use Spryker\Shared\TestIntegrator\TestIntegratorAddArrayElement;
use Spryker\Shared\TestIntegrator\TestIntegratorAddArrayElementAfter;
use Spryker\Shared\TestIntegrator\TestIntegratorAddArrayElementBefore;

class TestIntegratorAddArrayElementConfig
{
    protected function getTestConfiguration(): array
    {
        return [
            TestIntegratorAddArrayElementBefore::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST_BEFORE,
            TestIntegratorAddArrayElementAfter::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST_AFTER,
            TestIntegratorAddArrayElement::TEST_INTEGRATION_ADD_ARRAY_ELEMENT_CONST,
        ];
    }
}
