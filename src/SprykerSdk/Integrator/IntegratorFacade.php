<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

use SprykerSdk\Integrator\Common\AbstractFacade;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;

class IntegratorFacade extends AbstractFacade implements IntegratorFacadeInterface
{
    use IntegratorFactoryAwareTrait;

    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
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
