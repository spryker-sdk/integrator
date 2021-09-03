<?php

declare(strict_types=1);

namespace SprykerSdk\Shared\ModuleFinder;

use SprykerSdk\ModuleFinder\ModuleFinderFactory;

trait ModuleFinderFactoryAwareTrait
{
    /**
     * @return \SprykerSdk\ModuleFinder\ModuleFinderFactory
     */
    protected function getFactory(): ModuleFinderFactory
    {
        return new ModuleFinderFactory();
    }
}
