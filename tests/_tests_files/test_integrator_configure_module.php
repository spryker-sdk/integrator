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
    public function getTestConfiguration(): array
    {
        return [
            TestIntegratorConfigureModuleConfig::TEST_CONFIG_MODULE,
        ];
    }

    public function getTestConfigurationAssociativeArray(): array
    {
        return [
            '@CatalogPage/views/catalog-with-cms-slot/catalog-with-cms-slot.twig' => [
                \Spryker\Shared\CmsSlotBlockCategoryConnector\CmsSlotBlockCategoryConnectorConfig::CONDITION_KEY,
            ],
            '@Cms/templates/placeholders-title-content-slot/placeholders-title-content-slot.twig' => [
                \Spryker\Shared\CmsSlotBlockCategoryConnector\CmsSlotBlockCmsConnectorConfig::CONDITION_KEY,
            ],
            '@ProductDetailPage/views/pdp/pdp.twig' => [
                \Spryker\Shared\CmsSlotBlockCategoryConnector\CmsSlotBlockProductCategoryConnectorConfig::CONDITION_KEY,
            ],
        ];
    }

    public function getTestConfigurationAnyConstants(): array
    {
        return [
            \Spryker\Shared\CmsChartContentWidgetConfigurationProvider::FUNCTION_NAME => new \Spryker\Shared\CmsChartContentWidgetConfigurationProvider(),
            \Spryker\Shared\CmsProductContentWidgetConfigurationProvider::FUNCTION_NAME => new \Spryker\Shared\CmsProductContentWidgetConfigurationProvider(),
            \Spryker\Shared\CmsProductSetContentWidgetConfigurationProvider::FUNCTION_NAME => new \Spryker\Shared\CmsProductSetContentWidgetConfigurationProvider(),
        ];
    }

    public function getTestConfigurationPlusParentFields(): array
    {
        return [
            'sales' => '/sales/customer/customer-orders',
            'notes' => '/customer-note-gui/index/index',
        ] + parent::getCustomerDetailExternalBlocksUrls();
    }

    public function getTestConfigurationMergeParentFields(): array
    {
        return array_merge(parent::getMerchantOmsProcesses(), [
            static::MAIN_MERCHANT_OMS_PROCESS_NAME,
        ]);
    }

    public function getTestConfigurationMergeParentFields2(): array
    {
        return array_merge(parent::getMerchantProcessInitialStateMap(), [
            static::MAIN_MERCHANT_OMS_PROCESS_NAME => static::MAIN_MERCHANT_STATE_MACHINE_INITIAL_STATE,
        ]);
    }

    public function getTestConfigurationMergeParentFields3(): array
    {
        return array_merge(parent::getItemFieldsForIsSameItemComparison(), [
            ItemTransfer::MERCHANT_REFERENCE,
            ItemTransfer::PRODUCT_OFFER_REFERENCE,
        ]);
    }

    public function getTestConfigurationMergeParentFields4(): array
    {
        return array_merge(parent::getCorePropelSchemaPathPatterns(), [
            APPLICATION_VENDOR_DIR . '/spryker/spryker/Bundles/*/src/*/Zed/*/Persistence/Propel/Schema/',
        ]);
    }

    public function getTestConfigurationMergeParentFields5(): array
    {
        return array_merge(parent::getQuoteFieldsAllowedForSaving(), [
            QuoteTransfer::BUNDLE_ITEMS,
            QuoteTransfer::CART_NOTE,
            #CartNoteFeature,
            QuoteTransfer::EXPENSES,
        ]);
    }

    public function getTestConfigurationSophisticatedExpression() : array
    {
        $templateList = [
            CmsBlockCategoryConnectorConfig::CATEGORY_TEMPLATE_ONLY_CMS_BLOCK => '@CatalogPage/views/simple-cms-block/simple-cms-block.twig',
            CmsBlockCategoryConnectorConfig::CATEGORY_TEMPLATE_WITH_CMS_BLOCK => '@CatalogPage/views/catalog-with-cms-block/catalog-with-cms-block.twig',
        ];
        $templateList += parent::getTemplateList();
        return $templateList;
    }

    public function getTestConfigurationSophisticatedExpression2(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer) : array
    {
        $paymentMethodStatemachineMapping = $this->getPaymentMethodStatemachineMapping();
        if (!array_key_exists($quoteTransfer->getPayment()->getPaymentSelection(), $paymentMethodStatemachineMapping)) {
            return parent::determineProcessForOrderItem($quoteTransfer, $itemTransfer);
        }
        return $paymentMethodStatemachineMapping[$quoteTransfer->getPayment()->getPaymentSelection()];
    }
}
