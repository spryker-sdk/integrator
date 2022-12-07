<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Pyz\Shared\Scheduler\SchedulerConfig;
use Pyz\Zed\TestIntegratorWirePlugin\Plugin\ChildPlugin;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Log\LogConstants;
use Spryker\Zed\SchedulerJenkins\Communication\Plugin\Adapter\SchedulerJenkinsAdapterPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\AfterFirstPluginSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\AfterTestBarConditionPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\AvailabilityStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\BeforeAllPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\BeforeAllPluginsSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\FirstPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\FooStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\SecondPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\SinglePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestBarConditionPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestFooConditionPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorSingleWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginExpressionIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStaticIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStringIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\UrlStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\TestIntegratorWirePluginConfig;

class TestIntegratorWirePluginDependencyProvider
{
    public function getSinglePlugin(): TestIntegratorSingleWirePlugin
    {
        return new TestIntegratorSingleWirePlugin();
    }

    public function getTestPlugins(): array
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
            new BeforeAllPluginsSubscriber(),
            new FirstPlugin(),
            new AfterFirstPluginSubscriber(),
            new TestIntegratorWirePlugin(),
            new SecondPlugin(),
            new ChildPlugin(),
        ];
    }

    protected function getSchedulerAdapterPlugins(): array
    {
        return [
            SchedulerConfig::PYZ_SCHEDULER_JENKINS => new SchedulerJenkinsAdapterPlugin(),
        ];
    }

    protected function getEventListenerPluginsWithCollectionReturn(): Collection
    {
        $collection = new Collection();
        $collection->add(new BeforeAllPlugin());

        $collection->add(new FirstPlugin());
        $collection->add(new AfterFirstPluginSubscriber());
        $collection->add(new UrlStorageEventSubscriber());
        $collection->add(new AvailabilityStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithOrderedBeforeCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection->add(new UrlStorageEventSubscriber());
        $collection->add(new AvailabilityStorageEventSubscriber());
        $collection->add(new SecondPlugin());

        return $collection;
    }

    protected function getEventListenerPluginsWithOrderedAfterCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection->add(new FirstPlugin());
        $collection->add(new AvailabilityStorageEventSubscriber());
        $collection->add(new UrlStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithChainedCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection
            ->add(new UrlStorageEventSubscriber())
            ->add(new AvailabilityStorageEventSubscriber())->add(new FooStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithChainedOrderedBeforeCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection
            ->add(new UrlStorageEventSubscriber())->add(new FooStorageEventSubscriber())
            ->add(new AvailabilityStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithChainedOrderedAfterCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection
            ->add(new UrlStorageEventSubscriber())->add(new FooStorageEventSubscriber())
            ->add(new AvailabilityStorageEventSubscriber());

        return $collection;
    }

    protected function extendConditionPlugins(Container $container): Container
    {
        $container->extend('TEST_PLUGINS', function (ConditionCollectionInterface $conditionCollection) {
            $conditionCollection->add(new BeforeAllPluginsSubscriber());
            $conditionCollection->add(new TestFooConditionPlugin());
            $conditionCollection->add(new TestBarConditionPlugin());
            $conditionCollection->add(new AfterTestBarConditionPlugin());

            return $conditionCollection;
        });

        return $container;
    }

    protected function extendConditionKeyValuePlugins(Container $container): Container
    {
        $container->extend('TEST_PLUGINS', function (ConditionCollectionInterface $conditionCollection) {
            $conditionCollection->add('Oms/SendOrderShipped', new FirstPlugin());
            $conditionCollection->add(new TestBarConditionPlugin(), 'Oms/SendOrderShipped');
            $conditionCollection->add('Oms/SendOrderShipped', new TestFooConditionPlugin());

            return $conditionCollection;
        });

        return $container;
    }

    public function getTestArrayMergePlugins(): array
    {
        return array_merge([\ArrayObject::class], [
            new TestIntegratorWirePlugin(),
        ], [
            TestIntegratorWirePluginConfig::TEST_INTEGRATOR_WIRE_PLUGIN => new TestIntegratorWirePluginIndex(),
        ], [
            static::TEST_INTEGRATOR_WIRE_PLUGIN_STATIC_INDEX => new TestIntegratorWirePluginStaticIndex(),
        ], [
            'TEST_INTEGRATOR_WIRE_PLUGIN_STRING_INDEX' => new TestIntegratorWirePluginStringIndex(),
        ]);
    }
}
