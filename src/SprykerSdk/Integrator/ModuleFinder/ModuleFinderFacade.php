<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder;

use SprykerSdk\Integrator\ModuleFinderFactoryAwareTrait;
use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;

class ModuleFinderFacade implements ModuleFinderFacadeInterface
{
    use ModuleFinderFactoryAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer[]
     */
    public function getProjectModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        return $this->getFactory()->createProjectModuleFinder()->getProjectModules($moduleFilterTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer[]
     */
    public function getModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        return $this->getFactory()->createModuleFinder()->getModules($moduleFilterTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @return \SprykerSdk\Integrator\Transfer\PackageTransfer[]
     */
    public function getPackages(): array
    {
        return $this->getFactory()->createPackageFinder()->getPackages();
    }
}
