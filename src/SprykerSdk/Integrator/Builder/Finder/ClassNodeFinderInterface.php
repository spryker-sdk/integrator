<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Finder;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface ClassNodeFinderInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     *
     * @return \PhpParser\Node\Stmt\ClassMethod|null
     */
    public function findMethodNode(ClassInformationTransfer $classInformationTransfer, string $targetMethodName);

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetNodeName
     *
     * @return \PhpParser\Node\Stmt\ClassConst|null
     */
    public function findConstantNode(ClassInformationTransfer $classInformationTransfer, string $targetNodeName);

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return \PhpParser\Node\Stmt\Class_|null
     */
    public function findClassNode(ClassInformationTransfer $classInformationTransfer);
}
