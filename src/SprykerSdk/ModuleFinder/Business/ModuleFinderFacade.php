<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\ModuleFinder\Business;

use SprykerSdk\Shared\Transfer\ModuleFilterTransfer;
use SprykerSdk\Shared\Common\AbstractFacade;
use SprykerSdk\Shared\ModuleFinder\ModuleFinderFactoryAwareTrait;

class ModuleFinderFacade extends AbstractFacade implements ModuleFinderFacadeInterface
{
    use ModuleFinderFactoryAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \SprykerSdk\Shared\Transfer\ModuleTransfer[]
     */
    public function getProjectModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        return $this->getFactory()->createProjectModuleFinder()->getProjectModules($moduleFilterTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \SprykerSdk\Shared\Transfer\ModuleTransfer[]
     */
    public function getModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        return $this->getFactory()->createModuleFinder()->getModules($moduleFilterTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @internal
     *
     * @return \SprykerSdk\Shared\Transfer\PackageTransfer[]
     */
    public function getPackages(): array
    {
        return $this->getFactory()->createPackageFinder()->getPackages();
    }
}
