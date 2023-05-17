<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Spryker\Shared\Config\Config;
use Spryker\Shared\Log\LogConstants;
use Pyz\Zed\TestIntegratorWirePlugin\Plugin\ChildPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\SinglePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\FirstPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\SecondPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\UrlStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\AvailabilityStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestFooConditionPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginExpressionIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStaticIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStringIndex;
use Spryker\Zed\TestIntegratorWirePlugin\TestIntegratorWirePluginConfig;

class TestIntegratorWirePluginDependencyProvider extends TestParentIntegratorWirePluginDependencyProvider
{
    public function getSinglePlugin(): SinglePlugin
    {
        return new SinglePlugin();
    }

    public function getConditionPlugins(): array
    {
        $plugins = [];

        return $plugins;
    }

    public function getTestPlugins(): array
    {
        return [];
    }

    public function getTestAlreadyAddedPlugins(): array
    {
        return [
            new TestIntegratorWirePlugin(),
            TestIntegratorWirePluginConfig::TEST_INTEGRATOR_WIRE_PLUGIN => new TestIntegratorWirePluginIndex(),
            static::TEST_INTEGRATOR_WIRE_PLUGIN_STATIC_INDEX => new TestIntegratorWirePluginStaticIndex(),
            'TEST_INTEGRATOR_WIRE_PLUGIN_STRING_INDEX' => new TestIntegratorWirePluginStringIndex(),
            Config::get(LogConstants::LOG_QUEUE_NAME) => new TestIntegratorWirePluginExpressionIndex(),
        ];
    }

    public function getOrderedTestPlugins(): array
    {
        return [
            new FirstPlugin(),
            new SecondPlugin(),
            new ChildPlugin(),
        ];
    }

    protected function getSchedulerAdapterPlugins(): array
    {
        return [
        ];
    }

    protected function getEventListenerPluginsWithCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection->add(new FirstPlugin());
        $collection->add(new UrlStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithOrderedBeforeCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection->add(new UrlStorageEventSubscriber());
        $collection->add(new SecondPlugin());

        return $collection;
    }

    protected function getEventListenerPluginsWithOrderedAfterCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection->add(new FirstPlugin());
        $collection->add(new UrlStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithChainedCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection
            ->add(new UrlStorageEventSubscriber())
            ->add(new AvailabilityStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithChainedOrderedBeforeCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection
            ->add(new UrlStorageEventSubscriber())
            ->add(new AvailabilityStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithChainedOrderedAfterCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection
            ->add(new UrlStorageEventSubscriber())
            ->add(new AvailabilityStorageEventSubscriber());

        return $collection;
    }

    protected function extendConditionPlugins(Container $container): Container
    {
        $container->extend('TEST_PLUGINS', function (ConditionCollectionInterface $conditionCollection) {
            $conditionCollection->add(new TestFooConditionPlugin());

            return $conditionCollection;
        });

        return $container;
    }

    protected function extendConditionKeyValuePlugins(Container $container): Container
    {
        $container->extend('TEST_PLUGINS', function (ConditionCollectionInterface $conditionCollection) {
            $conditionCollection->add('Oms/SendOrderShipped', new FirstPlugin());

            return $conditionCollection;
        });

        return $container;
    }

    public function getTestArrayMergePlugins(): array
    {
        return array_merge(parent::getTestArrayMergePlugins(), []);
    }

    /**
     * @return array
     */
    protected function getWrappedPlugins(): array
    {
        return array_merge(
            $this->getWrappedFunctionDefault()
        );
    }
    /**
     * @return array
     */
    protected function getWrappedFunctionDefault(): array
    {
        return [];
    }
    /**
     * @return array
     */
    protected function getWrappedPluginsWithIndex(): array
    {
        return array_merge(
            ['indexDefault' => $this->getWrappedFunctionWithIndexD()]
        );
    }
    /**
     * @return array
     */
    public function getWrappedFunctionWithIndexD() : array
    {
        return [
            new Plugin1(),
        ];
    }

    protected function getWrappedFunctionsWithIndex(): array
    {
        return [
            'indexDefault' => $this->getWrappedFunctionWithIndexA(),
        ];
    }

    public function getWrappedFunctionWithIndexA() : array
    {
        return [
            new Plugin1(),
        ];
    }
}
