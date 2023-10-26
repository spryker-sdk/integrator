<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Finder;

use PhpParser\Node\Stmt\ClassConst;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface ClassConstantFinderInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     *
     * @return \PhpParser\Node\Stmt\ClassConst|null
     */
    public function findConstantByName(ClassInformationTransfer $classInformationTransfer, string $constantName): ?ClassConst;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     *
     * @return \PhpParser\Node\Stmt\ClassConst|null
     */
    public function findParentConstantByName(ClassInformationTransfer $classInformationTransfer, string $constantName): ?ClassConst;
}
