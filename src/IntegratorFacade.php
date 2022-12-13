<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Transfer\SourceInputTransfer;

class IntegratorFacade implements IntegratorFacadeInterface
{
    use IntegratorFactoryAwareTrait;

    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param \SprykerSdk\Integrator\Transfer\SourceInputTransfer $sourceInputTransfer
     * @param bool $isDry
     *
     * @return int
     */
    public function runInstallation(array $moduleTransfers, InputOutputInterface $input, SourceInputTransfer $sourceInputTransfer, bool $isDry = false): int
    {
        return $this->getFactory()
            ->creatManifestExecutor()
            ->runModuleManifestExecution($moduleTransfers, $input, $isDry);
    }
}
