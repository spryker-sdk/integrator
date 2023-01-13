<?php

namespace Pyz\Client\TestIntegratorAddConfigArrayElement;

use Spryker\Client\TestIntegratorAddConfigArrayElement\TestIntegratorAddConfigArrayElementConfig as SprykerTestIntegratorAddConfigArrayElementConfig;

class TestIntegratorAddConfigArrayElementConfig extends SprykerTestIntegratorAddConfigArrayElementConfig
{
    public const TEST_VALUE_CHANGING = 'Some value';

    protected function getTestConfiguration(): array
    {
        return [];
    }

    protected function getTestArrayMergeConfiguration(): array
    {
        return array_merge(['SomeConf']);
    }
}
