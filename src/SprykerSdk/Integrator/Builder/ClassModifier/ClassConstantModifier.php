<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Builder\Visitor\AddConstantVisitor;
use SprykerSdk\Shared\Transfer\ClassInformationTransfer;

class ClassConstantModifier
{
    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder
     */
    protected $classNodeFinder;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder $classNodeFinder
     */
    public function __construct(ClassNodeFinder $classNodeFinder)
    {
        $this->classNodeFinder = $classNodeFinder;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setConstant(ClassInformationTransfer $classInformationTransfer, string $constantName, $value): ClassInformationTransfer
    {
        $parentConstant = $this->classNodeFinder->findConstantNode($classInformationTransfer, $constantName);
        $modifier = 'public';
        if ($parentConstant) {
            if ($parentConstant->isProtected()) {
                $modifier = 'protected';
            } elseif ($parentConstant->isPrivate()) {
                $modifier = 'private';
            }
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(
            new AddConstantVisitor(
                $constantName,
                $value,
                $modifier
            )
        );

        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }
}
