<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\CartPage;

use SprykerShop\Yves\CartPage\CartPageConfig as SprykerCartPageConfig;

class CartPageConfig extends SprykerCartPageConfig
{
    /**
     * @var array<int>
     */
    protected const ARRAY_VALUE = [10, 1000];

    /**
     * @var array<string, string>
     */
    protected const ASSOC_ARRAY_VALUE = [
        'key_1' => 'key_1_value',
        'key_2' => 'key_2_value',
    ];

    /**
     * @var bool
     */
    protected const BOOL_VALUE = true;

    /**
     * @return string
     */
    public function getConfigMethod(): string
    {
        return SprykerCartPageConfig::SOME_VALUE;
    }
}
