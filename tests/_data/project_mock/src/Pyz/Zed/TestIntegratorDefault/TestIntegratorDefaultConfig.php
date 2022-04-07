<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorDefault;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class TestIntegratorDefaultConfig
{
    public function getTestConfigurationValue(): string
    {
        return TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE_TO_CHANGE;
    }

    public function getTestConfigurationValue2(): string
    {
        return $this->getPathToRoot();
    }

    public function getTestConfigurationValue3(): string
    {
        return APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'phpcs_to_change.xml';
    }

    public function getTestConfigurationArray(): array
    {
        return [
            TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE_TO_CHANGE,
        ];
    }

    public function getTestConfigurationArray2(): array
    {
        return [
            TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE_TO_CHANGE => true,
        ];
    }

    public function getTestConfigurationIsLiteralExpression(): array
    {
        return [];
    }

    public function getTestConfigurationIsLiteralExpression2(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer): array
    {
        return [];
    }
}
