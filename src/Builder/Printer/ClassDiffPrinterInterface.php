<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Printer;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface ClassDiffPrinterInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return string
     */
    public function printDiff(ClassInformationTransfer $classInformationTransfer): string;
}
