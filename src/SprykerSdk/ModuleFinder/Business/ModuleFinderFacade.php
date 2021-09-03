<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\ModuleFinder\Business;

use Generated\Shared\Transfer\ModuleFilterTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \SprykerSdk\ModuleFinder\Business\ModuleFinderBusinessFactory getFactory()
 * @method \SprykerSdk\ModuleFinder\Persistence\ModuleFinderRepositoryInterface getRepository()
 * @method \SprykerSdk\ModuleFinder\Persistence\ModuleFinderEntityManagerInterface getEntityManager()
 */
class ModuleFinderFacade extends AbstractFacade implements ModuleFinderFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \Generated\Shared\Transfer\ModuleTransfer[]
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
     * @param \Generated\Shared\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \Generated\Shared\Transfer\ModuleTransfer[]
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
     * @return \Generated\Shared\Transfer\PackageTransfer[]
     */
    public function getPackages(): array
    {
        return $this->getFactory()->createPackageFinder()->getPackages();
    }
}
