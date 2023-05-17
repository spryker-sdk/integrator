<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorWirePlugin;

use Pyz\Shared\Scheduler\SchedulerConfig;
use Pyz\Zed\CustomNamespace\PluginParam;
use Pyz\Zed\CustomNamespace\PluginParam1;
use Pyz\Zed\CustomNamespace\PluginParam2;
use Pyz\Zed\TestIntegratorWirePlugin\Plugin\ChildPlugin;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Log\LogConstants;
use Spryker\Zed\SchedulerJenkins\Communication\Plugin\Adapter\SchedulerJenkinsAdapterPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\AfterFirstPluginSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\AfterTestBarConditionPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\AvailabilityStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\BeforeAllPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\BeforeAllPluginsSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\CustomerUnsubscribePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\FirstPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\FooStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\NewsletterConstants;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\Plugin1;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\Plugin2;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\SecondPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\SinglePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestAppendArgumentArrayValue;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestBarConditionPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestFooConditionPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorAfterAndBeforeWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorSingleWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePlugin;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginExpressionIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStaticIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\TestIntegratorWirePluginStringIndex;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\UrlStorageEventSubscriber;
use Spryker\Zed\TestIntegratorWirePlugin\Communication\Plugin\WebProfilerApplicationPlugin;
use Spryker\Zed\TestIntegratorWirePlugin\TestIntegratorWirePluginConfig;

class TestIntegratorWirePluginDependencyProvider extends TestParentIntegratorWirePluginDependencyProvider
{
    public function getSinglePlugin(): TestIntegratorSingleWirePlugin
    {
        return new TestIntegratorSingleWirePlugin();
    }

    public function getConditionPlugins(): array
    {
        $plugins = [
            new CustomerUnsubscribePlugin([
                NewsletterConstants::DEFAULT_NEWSLETTER_TYPE,
            ]),
        ];
        if (class_exists(WebProfilerApplicationPlugin::class)) {
            $plugins[] = new WebProfilerApplicationPlugin();
        }

        return $plugins;
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
            new BeforeAllPluginsSubscriber(),
            new FirstPlugin(),
            new AfterFirstPluginSubscriber(),
            new TestIntegratorAfterAndBeforeWirePlugin(),
            new TestIntegratorWirePlugin(),
            new SecondPlugin(),
            new ChildPlugin(),
        ];
    }

    protected function getSchedulerAdapterPlugins(): array
    {
        return [
            $this->getWrappedFunctionC(),
            $this->getWrappedFunctionD(),
            SchedulerConfig::PYZ_SCHEDULER_JENKINS => new SchedulerJenkinsAdapterPlugin(),
            $this->getWrappedFunction1(),
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
            $conditionCollection->add(new TestAppendArgumentArrayValue(), [
                'static::Oms/SendOrderShipped',
            ]);
            $conditionCollection->add('Oms/SendOrderShipped', new TestFooConditionPlugin());

            return $conditionCollection;
        });

        return $container;
    }

    public function getTestArrayMergePlugins(): array
    {
        return array_merge(parent::getTestArrayMergePlugins(), [
            new TestIntegratorWirePlugin(),
            TestIntegratorWirePluginConfig::TEST_INTEGRATOR_WIRE_PLUGIN => new TestIntegratorWirePluginIndex(),
            static::TEST_INTEGRATOR_WIRE_PLUGIN_STATIC_INDEX => new TestIntegratorWirePluginStaticIndex(),
            'TEST_INTEGRATOR_WIRE_PLUGIN_STRING_INDEX' => new TestIntegratorWirePluginStringIndex(),
        ]);
    }

    /**
     * @return array
     */
    protected function getWrappedPlugins(): array
    {
        return array_merge(
            $this->getWrappedFunctionDefault(), $this->getWrappedFunctionA(), $this->getWrappedFunctionB(), [
                new TestIntegratorWirePlugin(new PluginParam(), [
                    new PluginParam1(),
                    new PluginParam2(),
                ]),
            ]
        );
    }
    /**
     * @return array
     */
    protected function getWrappedFunctionDefault(): array
    {
        return [
            new FirstPlugin(),
        ];
    }
    /**
     * @return array
     */
    protected function getWrappedPluginsWithIndex(): array
    {
        return array_merge(
            ['indexDefault' => $this->getWrappedFunctionWithIndexD()], [
                'indexKey' => $this->getWrappedFunctionWithIndexC(),
            ]
        );
    }
    /**
     * @return array
     */
    public function getWrappedFunctionWithIndexD() : array
    {
        return [
            new Plugin1(), new FirstPlugin(),
        ];
    }

    protected function getWrappedFunctionsWithIndex(): array
    {
        return [
            'indexDefault' => $this->getWrappedFunctionWithIndexA(), 'indexKey' => $this->getWrappedFunctionWithIndexB(),
        ];
    }

    public function getWrappedFunctionWithIndexA() : array
    {
        return [
            new Plugin1(), 'key' => new Plugin2(),
        ];
    }
    /**
     * @return array
     */
    public function getWrappedFunctionB() : array
    {
        return [
            new SecondPlugin(),
        ];
    }
    /**
     * @return array
     */
    public function getWrappedFunctionA() : array
    {
        return [
            new FirstPlugin(),
        ];
    }
    /**
     * @return array
     */
    public function getWrappedFunctionWithIndexC() : array
    {
        return [
            new FirstPlugin(),
        ];
    }
    public function getConditionParentPlugins() : array
    {
        $plugins = [
        ];
        if (class_exists(WebProfilerApplicationPlugin::class)) {
            $plugins[] = new WebProfilerApplicationPlugin();
        }
        return $plugins;
    }
    /**
     * @return array
     */
    public function getWrappedFunction1() : array
    {
        return [
            new Plugin1(),
        ];
    }
    /**
     * @return array
     */
    public function getWrappedFunctionC() : array
    {
        return [
            new Plugin1(),
        ];
    }
    /**
     * @return array
     */
    public function getWrappedFunctionD() : array
    {
        return [
            new Plugin1(),
        ];
    }
    /**
     * @return array
     */
    public function getWrappedFunctionWithIndexB() : array
    {
        return [
            new Plugin2(),
        ];
    }
}
