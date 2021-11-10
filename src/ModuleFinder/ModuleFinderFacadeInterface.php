<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder;

use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;

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
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    public function getModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array;

    /**
     * Specification:
     * - Finds all project modules.
     *
     * @api
     *
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
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
     * @return array<\SprykerSdk\Integrator\Transfer\PackageTransfer>
     */
    public function getPackages(): array;
}
