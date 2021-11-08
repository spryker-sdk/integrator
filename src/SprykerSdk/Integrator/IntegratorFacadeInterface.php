<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;

interface IntegratorFacadeInterface
{
    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param bool $isDry
     *
     * @return int
     */
    public function runInstallation(array $moduleTransfers, InputOutputInterface $input, bool $isDry = false): int;
}
