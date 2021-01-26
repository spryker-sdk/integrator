<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business;

use SprykerSdk\Zed\Integrator\Dependency\Console\InputOutputInterface;

/**
 * @method \SprykerSdk\Zed\Integrator\Business\IntegratorBusinessFactory getFactory()
 */
interface IntegratorFacadeInterface
{
    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ModuleTransfer[] $moduleTransfers
     * @param \SprykerSdk\Zed\Integrator\Dependency\Console\InputOutputInterface $input
     * @param bool $isDry
     *
     * @return int
     */
    public function runInstallation(array $moduleTransfers, InputOutputInterface $input, bool $isDry = false): int;
}
