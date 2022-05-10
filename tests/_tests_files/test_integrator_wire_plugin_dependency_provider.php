<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\AvailabilityStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\FirstPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\SecondPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;

class TestIntegratorWirePluginDependencyProvider
{
    public function getTestPlugins(): array
    {
        return [
            new TestIntegratorWirePlugin(),
        ];
    }

    public function getOrderedTestPlugins(): array
    {
        return [
            new FirstPlugin(),
            new TestIntegratorWirePlugin(),
            new SecondPlugin(),
        ];
    }

    protected function getEventListenerPluginsWithCollectionReturn(): Collection
    {
        $collection = new Collection();

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
            $conditionCollection->add(new TestFooConditionPlugin());
            $conditionCollection->add(new TestBarConditionPlugin());

            return $conditionCollection;
        });

        return $container;
    }
}
