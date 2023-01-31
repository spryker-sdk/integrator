<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

class IntegratorFacade implements IntegratorFacadeInterface
{
    use IntegratorFactoryAwareTrait;

    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function runModuleManifestInstallation(
        array $moduleTransfers,
        InputOutputInterface $input,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void {
        $this->getFactory()
            ->createModuleManifestExecutor()
            ->runModuleManifestExecution($moduleTransfers, $input, $commandArgumentsTransfer);
    }

    /**
     * @param int $releaseGroupId
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function runReleaseGroupManifestInstallation(
        int $releaseGroupId,
        InputOutputInterface $input,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void {
        $this->getFactory()
            ->createReleaseGroupManifestExecutor()
            ->runReleaseGroupManifestExecution($releaseGroupId, $input, $commandArgumentsTransfer);
    }
}
