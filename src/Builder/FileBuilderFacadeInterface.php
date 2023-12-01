<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder;

use SprykerSdk\Integrator\Transfer\FileInformationTransfer;

interface FileBuilderFacadeInterface
{
    /**
     * @param string $path
     *
     * @return \SprykerSdk\Integrator\Transfer\FileInformationTransfer
     */
    public function loadFile(string $path): FileInformationTransfer;

    /**
     * @param \SprykerSdk\Integrator\Transfer\FileInformationTransfer $fileInformationTransfer
     *
     * @return bool
     */
    public function storeFile(FileInformationTransfer $fileInformationTransfer): bool;
}
