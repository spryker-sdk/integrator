<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\ModuleFinder\Business\Module\ModuleMatcher;

use Shared\Transfer\ModuleFilterTransfer;
use Shared\Transfer\ModuleTransfer;

interface ModuleMatcherInterface
{
    /**
     * @param \Shared\Transfer\ModuleTransfer $moduleTransfer
     * @param \Shared\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     *
     * @return bool
     */
    public function matches(ModuleTransfer $moduleTransfer, ModuleFilterTransfer $moduleFilterTransfer): bool;
}
