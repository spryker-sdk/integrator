<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\ModuleFinder\Business\Module\ProjectModuleFinder;

use SprykerSdk\Shared\Transfer\ModuleFilterTransfer;

interface ProjectModuleFinderInterface
{
    /**
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \SprykerSdk\Shared\Transfer\ModuleTransfer[]
     */
    public function getProjectModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array;
}
