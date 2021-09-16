<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher;

use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;

interface ModuleMatcherInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     *
     * @return bool
     */
    public function matches(ModuleTransfer $moduleTransfer, ModuleFilterTransfer $moduleFilterTransfer): bool;
}
