<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\ModuleFinder\Business;

use SprykerSdk\Shared\Transfer\ModuleFilterTransfer;

interface ModuleFinderFacadeInterface
{
    /**
     * Specification:
     * - Gets all modules.
     * - Creates an array of ModuleTransfer objects.
     * - The key of the returned array is `OrganizationName.ModuleName`.
     * - A ModuleFilterTransfer can be used to filter the returned collection.
     *
     * @api
     *
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \SprykerSdk\Shared\Transfer\ModuleTransfer[]
     */
    public function getModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array;

    /**
     * Specification:
     * - Finds all project modules.
     *
     * @api
     *
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \SprykerSdk\Shared\Transfer\ModuleTransfer[]
     */
    public function getProjectModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array;

    /**
     * Specification:
     * - Returns a list of packages defined in the Spryker namespace.
     * - Packages are not spryker modules.
     *
     * @api
     *
     * @internal
     *
     * @return \SprykerSdk\Shared\Transfer\PackageTransfer[]
     */
    public function getPackages(): array;
}
