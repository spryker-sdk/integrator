<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileWriter;

use SprykerSdk\Integrator\Transfer\FileInformationTransfer;

interface FileWriterInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\FileInformationTransfer $classInformationTransfer
     *
     * @return bool
     */
    public function storeFile(FileInformationTransfer $classInformationTransfer): bool;
}
