<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Transfer\SourceInputTransfer;

interface ManifestExecutorInterface
{
    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param \SprykerSdk\Integrator\Transfer\SourceInputTransfer $sourceInputTransfer
     * @param bool $isDry
     *
     * @return int
     */
    public function runModuleManifestExecution(array $moduleTransfers, InputOutputInterface $inputOutput, SourceInputTransfer $sourceInputTransfer, bool $isDry): int;
}
