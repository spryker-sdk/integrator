<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder\Module\ProjectModuleFinder;

use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;

interface ProjectModuleFinderInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    public function getProjectModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array;
}
