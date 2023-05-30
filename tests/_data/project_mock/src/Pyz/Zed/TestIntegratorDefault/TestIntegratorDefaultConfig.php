<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorDefault;

use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\SinglePlugin;

class TestIntegratorDefaultConfig extends BaseConfig
{
    public const BOOL_EXISTING_VALUE = 'false';
    /**
     * @return string
     */
    public function testChange(): string
    {
        return 'test';
    }

    /**
     * @return string
     */
    public function testChangeMissingValue(): string
    {
        return 'test';
    }

    /**
     * @return array
     */
    public function testChange2(): array
    {
        return [
            static::TEST_VARIABLE,
        ];
    }

    /**
     * @return array
     */
    public function testChange3(): array
    {
        return [
            static::TEST_VARIABLE => static::ANOTHER_TEST_VARIABLE,
        ];
    }

    /**
     * @return array
     */
    public function testChange4(): array
    {
        return [
            static::TEST_VARIABLE => [
                static::ANOTHER_TEST_VARIABLE,
            ],
        ];
    }

    /**
     * @return array
     */
    public function testChange5(): array
    {
        return array_merge(
            parent::isCartCartItemsViaAjaxLoadEnabled(),
            parent::getSharedConfig(),
            $this->getSharedConfig2(),
        );
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function testChange7(): array
    {
        return array_merge(
            parent::isCartCartItemsViaAjaxLoadEnabled(),
            [
                static::BOOL_VALUE,
            ]
        );
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function testChangeMethod9() : array
    {
        return [
            static::IS_CART_CART_ITEMS_VIA_AJAX_LOAD_ENABLED => false,
        ];
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function testChangeArrayMerge(): array
    {
        return array_merge(
            parent::isCartCartItemsViaAjaxLoadEnabled(),
            parent::getSharedConfig(),
        );
    }

    /**
     * @return array
     */
    public function testAlreadyAddedConfigurationValue(): array
    {
        return [
            SinglePlugin::CONST_VALUE,
        ];
    }

    /**
     * @return array
     */
    public function getProtectedPaths(): array
    {
        return [
            '/categories' => [
                'isRegularExpression' => false,
            ],
            '#^/categories/.*#' => [
                'isRegularExpression' => true,
            ],
        ];
    }
}
