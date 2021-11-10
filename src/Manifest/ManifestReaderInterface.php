<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Manifest;

interface ManifestReaderInterface
{
    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     *
     * @return array<string, array<string, array<string>>>
     */
    public function readManifests(array $moduleTransfers): array;
}
