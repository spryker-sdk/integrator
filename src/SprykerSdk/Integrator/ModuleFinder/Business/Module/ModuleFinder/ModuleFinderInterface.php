<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\ModuleFinder\Business\Module\ModuleFinder;

use SprykerSdk\Shared\Transfer\ModuleFilterTransfer;

interface ModuleFinderInterface
{
    /**
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return \SprykerSdk\Shared\Transfer\ModuleTransfer[]
     */
    public function getModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array;
}
