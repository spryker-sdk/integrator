<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

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
