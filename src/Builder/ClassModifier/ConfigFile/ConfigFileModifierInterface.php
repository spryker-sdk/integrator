<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ConfigFile;

use SprykerSdk\Integrator\Transfer\FileInformationTransfer;

interface ConfigFileModifierInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\FileInformationTransfer $fileInformationTransfer
     * @param string $target
     * @param string $value
     *
     * @return \SprykerSdk\Integrator\Transfer\FileInformationTransfer
     */
    public function addArrayItemToEnvConfig(FileInformationTransfer $fileInformationTransfer, string $target, string $value): FileInformationTransfer;
}
