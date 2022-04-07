<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorDefault;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\TestIntegratorConfigureModule\TestIntegratorConfigureModuleConfig;

class TestIntegratorDefaultConfig
{
    public function getTestConfigurationValue(): string
    {
        return TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE;
    }

    public function getTestConfigurationValue2(): string
    {
        return $this->getPathToRoot() . 'vendor/spryker/spryker/Bundles/';
    }

    public function getTestConfigurationValue3(): string
    {
        return APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'phpcs.xml';
    }

    public function getTestConfigurationArray(): array
    {
        return [
            TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE_TO_CHANGE,
            TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE_ADD,
        ];
    }

    public function getTestConfigurationArray2(): array
    {
        return [
            TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE_TO_CHANGE => true,
            TestIntegratorConfigureModuleConfig::CONTENT_TYPE_PRICE => true,
        ];
    }

    public function getTestConfigurationIsLiteralExpression() : array
    {
        $templateList = [
            CmsBlockCategoryConnectorConfig::CATEGORY_TEMPLATE_ONLY_CMS_BLOCK => '@CatalogPage/views/simple-cms-block/simple-cms-block.twig',
            CmsBlockCategoryConnectorConfig::CATEGORY_TEMPLATE_WITH_CMS_BLOCK => '@CatalogPage/views/catalog-with-cms-block/catalog-with-cms-block.twig',
        ];
        $templateList += parent::getTemplateList();
        return $templateList;
    }

    public function getTestConfigurationIsLiteralExpression2(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer) : array
    {
        $paymentMethodStatemachineMapping = $this->getPaymentMethodStatemachineMapping();
        if (!array_key_exists($quoteTransfer->getPayment()->getPaymentSelection(), $paymentMethodStatemachineMapping)) {
            return parent::determineProcessForOrderItem($quoteTransfer, $itemTransfer);
        }
        return $paymentMethodStatemachineMapping[$quoteTransfer->getPayment()->getPaymentSelection()];
    }
}
