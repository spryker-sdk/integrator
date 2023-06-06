<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Manifest;

use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

interface RepositoryManifestReaderInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     * @param array<string, string> $lockedModules
     *
     * @return array<string, array<string, array<string>>>
     */
    public function readUnappliedManifests(IntegratorCommandArgumentsTransfer $commandArgumentsTransfer, array $lockedModules): array;
}
