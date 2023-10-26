<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Finder;

use PhpParser\Node\Stmt\ClassConst;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassConstantFinder implements ClassConstantFinderInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    protected ClassNodeFinderInterface $classNodeFinder;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     */
    public function __construct(ClassNodeFinderInterface $classNodeFinder)
    {
        $this->classNodeFinder = $classNodeFinder;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     *
     * @return \PhpParser\Node\Stmt\ClassConst|null
     */
    public function findConstantByName(ClassInformationTransfer $classInformationTransfer, string $constantName): ?ClassConst
    {
        $classConstant = $this->classNodeFinder->findConstantNode($classInformationTransfer, $constantName);

        if ($classConstant !== null) {
            return $classConstant;
        }

        $parentConst = $this->findParentConstantByName($classInformationTransfer, $constantName);

        return $parentConst !== null && !$parentConst->isPrivate() ? $parentConst : null;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     *
     * @return \PhpParser\Node\Stmt\ClassConst|null
     */
    public function findParentConstantByName(ClassInformationTransfer $classInformationTransfer, string $constantName): ?ClassConst
    {
        $parentConstant = null;

        do {
            $classInformationTransfer = $classInformationTransfer->getParent();

            if ($classInformationTransfer === null) {
                break;
            }

            $parentConstant = $this->classNodeFinder->findConstantNode($classInformationTransfer, $constantName);
        } while ($parentConstant === null);

        return $parentConstant;
    }
}
