<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor\ReleaseGroup;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

interface ReleaseGroupManifestExecutorInterface
{
    /**
     * @param int $releaseGroupId
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return int
     */
    public function runReleaseGroupManifestExecution(
        int $releaseGroupId,
        InputOutputInterface $inputOutput,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): int;
}
