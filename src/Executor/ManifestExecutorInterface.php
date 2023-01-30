<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

interface ManifestExecutorInterface
{
 /**
  * @param array<string, array<string, array<string, array<string>>>> $unappliedManifests
  * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
  * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
  *
  * @return void
  */
    public function applyManifestList(
        array $unappliedManifests,
        InputOutputInterface $inputOutput,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void;

    /**
     * @param array<string, array<string, array<string>>> $manifests
     * @param array<string, array<string, array<string>>> $lockedModules
     *
     * @return array<string, array<string, array<string, array<string>>>>
     */
    public function findUnappliedManifests(array $manifests, array $lockedModules): array;
}
