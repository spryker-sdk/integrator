<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorUnwirePlugin;

use Spryker\Zed\TestIntegratorDefault\Communication\Plugin\TestIntegratorDefault1Plugin;
use Spryker\Zed\TestIntegratorDefault\Communication\Plugin\TestIntegratorDefault2Plugin;
use Spryker\Zed\TestIntegratorUnwirePlugin\Communication\Plugin\TestFooConditionPlugin;
use Spryker\Zed\TestIntegratorUnwirePlugin\Communication\Plugin\TestBarConditionPlugin;
use Spryker\Zed\TestIntegratorUnwirePlugin\Communication\Plugin\UrlStorageEventSubscriber;
use Spryker\Zed\TestIntegratorUnwirePlugin\Communication\Plugin\FooStorageEventSubscriber;
use Spryker\Zed\TestIntegratorUnwirePlugin\Communication\Plugin\AvailabilityStorageEventSubscriber;

class TestIntegratorUnwirePluginDependencyProvider
{
    public function getTestPlugins(): array
    {
        return [
            new TestIntegratorDefault1Plugin(),
            new TestIntegratorDefault2Plugin(),
        ];
    }

    protected function getEventListenerPluginsWithCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection->add(new UrlStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithChainedCollectionReturn(): Collection
    {
        $collection = new Collection();

        $collection
            ->add(new UrlStorageEventSubscriber())
            ->add(new FooStorageEventSubscriber());

        return $collection;
    }

    protected function getEventListenerPluginsWithChainedCollectionReturnRemoveLast(): Collection
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
}
