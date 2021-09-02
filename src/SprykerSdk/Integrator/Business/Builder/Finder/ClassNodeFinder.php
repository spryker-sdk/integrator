<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Business\Builder\Finder;

use Generated\Shared\Transfer\ClassInformationTransfer;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;

class ClassNodeFinder
{
    /**
     * @param \Generated\Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     *
     * @return \PhpParser\Node\Stmt\ClassMethod|null
     */
    public function findMethodNode(ClassInformationTransfer $classInformationTransfer, string $targetMethodName): ?ClassMethod
    {
        /** @var \PhpParser\Node\Stmt\ClassMethod|null $node */
        $node = (new NodeFinder())->findFirst($classInformationTransfer->getClassTokenTree(), function (Node $node) use ($targetMethodName) {
            return $node instanceof ClassMethod
                && $node->name->toString() === $targetMethodName;
        });

        return $node;
    }

    /**
     * @param \Generated\Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetNodeName
     *
     * @return \PhpParser\Node\Stmt\ClassConst|null
     */
    public function findConstantNode(ClassInformationTransfer $classInformationTransfer, string $targetNodeName): ?ClassConst
    {
        /** @var \PhpParser\Node\Stmt\ClassConst|null $node */
        $node = (new NodeFinder())->findFirst($classInformationTransfer->getClassTokenTree(), function (Node $node) use ($targetNodeName) {
            if (!($node instanceof ClassConst)) {
                return false;
            }

            foreach ($node->consts as $const) {
                if ($const->name->name === $targetNodeName) {
                    return true;
                }
            }

            return false;
        });

        return $node;
    }

    /**
     * @param \Generated\Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return \PhpParser\Node\Stmt\Class_|null
     */
    public function findClassNode(ClassInformationTransfer $classInformationTransfer): ?Class_
    {
        /** @var \PhpParser\Node\Stmt\Class_|null $node */
        $node = (new NodeFinder())->findFirst($classInformationTransfer->getClassTokenTree(), function (Node $node) {
            return $node instanceof Class_;
        });

        return $node;
    }
}
