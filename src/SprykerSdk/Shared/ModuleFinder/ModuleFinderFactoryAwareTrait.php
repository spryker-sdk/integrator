<?php

declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

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
