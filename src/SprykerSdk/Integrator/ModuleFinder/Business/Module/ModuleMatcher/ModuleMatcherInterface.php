<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\ModuleFinder\Business\Module\ModuleMatcher;

use SprykerSdk\Shared\Transfer\ModuleFilterTransfer;
use SprykerSdk\Shared\Transfer\ModuleTransfer;

interface ModuleMatcherInterface
{
    /**
     * @param \SprykerSdk\Shared\Transfer\ModuleTransfer $moduleTransfer
     * @param \SprykerSdk\Shared\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     *
     * @return bool
     */
    public function matches(ModuleTransfer $moduleTransfer, ModuleFilterTransfer $moduleFilterTransfer): bool;
}
