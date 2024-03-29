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
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function runModuleManifestInstallation(
        InputOutputInterface $input,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void {
        $this->getFactory()
            ->createModuleManifestExecutor()
            ->runModuleManifestExecution($input, $commandArgumentsTransfer);
    }

    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function runUpdateLock(
        InputOutputInterface $input,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void {
        $this->getFactory()
            ->createModuleManifestExecutor()
            ->runUpdateLock($input, $commandArgumentsTransfer);
    }

    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     *
     * @return void
     */
    public function runCleanLock(InputOutputInterface $input): void
    {
        $this->getFactory()
            ->createModuleManifestExecutor()
            ->runCleanLock($input);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     *
     * @return void
     */
    public function generateDiff(
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer,
        InputOutputInterface $input
    ): void {
        $this->getFactory()
            ->createReleaseGroupManifestExecutor()
            ->generateDiff($commandArgumentsTransfer, $input);
    }
}
