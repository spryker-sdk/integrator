<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

interface IntegratorFacadeInterface
{
    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function runModuleManifestInstallation(
        InputOutputInterface $input,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void;

    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function runUpdateLock(
        InputOutputInterface $input,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void;

    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     *
     * @return void
     */
    public function runCleanLock(InputOutputInterface $input): void;

    /**
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     *
     * @return void
     */
    public function generateDiff(
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer,
        InputOutputInterface $input
    ): void;
}
