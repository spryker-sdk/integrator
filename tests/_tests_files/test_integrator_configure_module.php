<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorDefault;

use App\Manifest\Generator\ArrayConfigElementManifestStrategy;
use App\Manifest\Generator\ArrayConfigElementManifestStrategy2;
use App\Manifest\Generator\ArrayConfigElementManifestStrategyTest;

class TestIntegratorDefaultConfig extends BaseConfig
{
    public const BOOL_VALUE = 'true';
    public const ASSOC_ARRAY_VALUE = [
        'key_1' => 'key_1_value',
        'key_2' => 'key_2_value',
    ];
    public const ARRAY_VALUE = [
        10,
        1000,
    ];
    /**
     * @return string
     */
    public function testChange(): string
    {
        return static::TEST_VALUE_CHANGE;
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
            static::TEST_VARIABLE, static::TEST_VALUE4_CHANGE,
        ];
    }

    /**
     * @return array
     */
    public function testChange3(): array
    {
        return [
            static::TEST_VARIABLE => static::ANOTHER_TEST_VARIABLE, static::TEST_VALUE5_CHANGE => static::TEST_VALUE_CHANGE,
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
            ], static::TEST_VALUE6 => [
                ArrayConfigElementManifestStrategy::TEST_VALUE5,
            ], static::TEST_VALUE7 => [
                ArrayConfigElementManifestStrategy::MANIFEST_KEY => ArrayConfigElementManifestStrategy::TEST_VALUE3,
                ArrayConfigElementManifestStrategy::MANIFEST_KEY => ArrayConfigElementManifestStrategy2::TEST_VALUE4,
            ],
        ];
    }

    /**
     * @return array
     */
    public function testChange5(): array
    {
        return array_merge(parent::isCartCartItemsViaAjaxLoadEnabled(), parent::getSharedConfig(), $this->getSharedConfig2());
    }

    /**
     * @return array
     */
    public function testChange6(): array
    {
        $array = parent::isCartCartItemsViaAjaxLoadEnabledChanged();
        $array = array_merge($array, [
            \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_VALUE,
            \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_VALUE2,
        ]);

        return array_merge($array, [
            \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_VALUE => [
                \App\Manifest\Generator\ArrayConfigElementManifestStrategy::MANIFEST_KEY,
            ],
            \App\Manifest\Generator\ArrayConfigElementManifestStrategy::TEST_VALUE2 => [
                \App\Manifest\Generator\ArrayConfigElementManifestStrategy::MANIFEST_KEY22,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function testChange7(): array
    {
        return array_merge(
            parent::isCartCartItemsViaAjaxLoadEnabled(),
            [
                static::BOOL_VALUE, ArrayConfigElementManifestStrategy::TEST_CHANGE7,
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
                ], ArrayConfigElementManifestStrategy::NEW_VALUE => [
                    ArrayConfigElementManifestStrategy::TEST_NEW_VALUE,
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
            static::IS_CART_CART_ITEMS_VIA_AJAX_LOAD_ENABLED => false, static::IS_CART_CART_ITEMS_VIA_AJAX_LOAD_ENABLED_CHANGED => true,
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
            $this->getSharedConfig2(),
            parent::getSharedConfig2(),
        );
    }
    /**
     * @return array
     */
    public function testNewMethod() : array
    {
        return array_merge(parent::isCartCartItemsViaAjaxLoadEnabled(), [
            'sales' => '/sales/customer/customer-orders',
            'notes' => '/customer-note-gui/index/index',
        ]);
    }
    /**
     * @return array
     */
    public function testNewMethod2() : array
    {
        return array_merge(parent::isCartCartItemsViaAjaxLoadEnabled(), parent::getSharedConfig());
    }
    /**
     * @return string
     */
    public function testNewMethod3() : string
    {
        return ArrayConfigElementManifestStrategyTest::MANIFEST_KEY;
    }
    /**
     * @return array
     */
    public function testNewMethod4() : array
    {
        return [
            ArrayConfigElementManifestStrategy::MANIFEST_KEY,
            ArrayConfigElementManifestStrategy::MANIFEST_KEY,
            static::TEST_VALUE3,
        ];
    }
    /**
     * @return array
     */
    public function testNewMethod5() : array
    {
        return [
            ArrayConfigElementManifestStrategy::TEST_VALUE2 => ArrayConfigElementManifestStrategy::MANIFEST_KEY,
            static::TEST_VALUE3 => static::TEST_VALUE2,
            static::TEST_VALUE5 => static::TEST_VALUE3,
        ];
    }
    /**
     * @return array
     */
    public function testNewMethod8() : array
    {
        return [
            static::IS_CART_CART_ITEMS_VIA_AJAX_LOAD_ENABLED => false,
        ];
    }
    /**
     * @return bool
     */
    public function testNewMethod9() : bool
    {
        return false;
    }
    /**
     * @return string
     */
    public function testNewMethod10() : string
    {
        return $this->getSharedConfig($this->testChange(), [
            \Pyz\Yves\CartPage\CartPageConfig::IS_CART_CART_ITEMS_VIA_AJAX_LOAD_ENABLED => [
                \Pyz\Yves\CartPage\CartPageConfig::IS_CART_CART_ITEMS_VIA_AJAX_LOAD_ENABLED => 'test',
            ],
        ]);
    }
}
