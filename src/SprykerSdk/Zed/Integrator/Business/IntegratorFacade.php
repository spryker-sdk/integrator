<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;
use SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface;

/**
 * @method \SprykerSdk\Zed\Integrator\Business\IntegratorBusinessFactory getFactory()
 */
class IntegratorFacade extends AbstractFacade implements IntegratorFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ModuleTransfer[] $moduleTransfers
     * @param \SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface $input
     * @param bool $isDry
     *
     * @return int
     */
    public function runInstallation(array $moduleTransfers, IOInterface $input, bool $isDry = false): int
    {
        return $this->getFactory()
            ->creatManifestExecutor()
            ->runModuleManifestExecution($moduleTransfers, $input, $isDry);
    }
}
