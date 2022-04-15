<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorDefault;

class TestIntegratorDefaultConfig extends BaseConfig
{
    public function testChange(): string
    {
        return 'test';
    }

    public function testChange2(): array
    {
        return [
            static::TEST_VARIABLE,
        ];
    }

    public function testChange3(): array
    {
        return [
            static::TEST_VARIABLE => static::ANOTHER_TEST_VARIABLE,
        ];
    }

    public function testChange4(): array
    {
        return [
            static::TEST_VARIABLE => [
                static::ANOTHER_TEST_VARIABLE,
            ],
        ];
    }

    public function testChange5(): array
    {
        return array_merge(
            parent::isCartCartItemsViaAjaxLoadEnabled(),
            parent::getSharedConfig(),
            this->getSharedConfig2(),
        );
    }

    public function testChange6(): array
    {
        $array = parent::isCartCartItemsViaAjaxLoadEnabledChanged();
        $array = array_merge_recursive(
            $array,
            [
                \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_VALUE_NOT_CHANGED,
                \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_VALUE2_NOT_CHANGED
            ]
        );

        return array_merge(
            $array,
            [
                \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_VALUE_NOT_CHANGED => [
                    \App\Manifest\Generator\ArrayConfigElementManifestStrategy::MANIFEST_KEY_NOT_CHANGED
                ],
                \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_VALUE2_NOT_CHANGED => [
                    \App\Manifest\Generator\ArrayConfigElementManifestStrategy::MANIFEST_KEY22_NOT_CHANGED
                ]
            ]
        );
    }

    public function testChange7(): array
    {
        return array_merge(
            parent::isCartCartItemsViaAjaxLoadEnabled(),
            [
                static::BOOL_VALUE,
            ]
        );
    }

    public function testChange8(): array
    {
        return array_merge(
            parent::isCartCartItemsViaAjaxLoadEnabled(),
            [
                ArrayConfigElementManifestStrategy::BOOL_VALUE => [
                    ArrayConfigElementManifestStrategy::TEST_EXISTS_VALUE,
                ],
            ]
        );
    }

    public function testNotChange(): array
    {
        return array_merge(
            $array,
            [
                \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_NOT_CHANGE => [
                    \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_NOT_CHANGE
                ],
                \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_NOT_CHANGE => [
                    \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_NOT_CHANGE
                ]
            ]
        );
    }

    public function testChangeArrayMerge(): array
    {
        return array_merge(
            parent::isCartCartItemsViaAjaxLoadEnabled(),
            parent::getSharedConfig(),
        );
    }
}
