<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Business;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Shared\Common\AbstractFacade;
use SprykerSdk\Shared\Integrator\IntegratorFactoryAwareTrait;

class IntegratorFacade extends AbstractFacade implements IntegratorFacadeInterface
{
    use IntegratorFactoryAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \SprykerSdk\Shared\Transfer\ModuleTransfer[] $moduleTransfers
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param bool $isDry
     *
     * @return int
     */
    public function runInstallation(array $moduleTransfers, InputOutputInterface $input, bool $isDry = false): int
    {
        return $this->getFactory()
            ->creatManifestExecutor()
            ->runModuleManifestExecution($moduleTransfers, $input, $isDry);
    }
}
