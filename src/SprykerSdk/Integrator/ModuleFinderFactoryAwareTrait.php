<?php

declare(strict_types=1);

namespace SprykerSdk\Shared\ModuleFinder;

use SprykerSdk\Integrator\ModuleFinder\ModuleFinderFactory;

trait ModuleFinderFactoryAwareTrait
{
    /**
     * @return \SprykerSdk\Integrator\ModuleFinder\ModuleFinderFactory
     */
    protected function getFactory(): ModuleFinderFactory
    {
        return new ModuleFinderFactory();
    }
}
