<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor\ReleaseGroup;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

interface DiffGeneratorInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     *
     * @return void
     */
    public function generateDiff(
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer,
        InputOutputInterface $inputOutput
    ): void;
}
