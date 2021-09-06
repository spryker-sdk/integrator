<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Business\Builder\ClassModifier;

use Shared\Transfer\ClassInformationTransfer;
use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Business\Builder\Visitor\AddConstantVisitor;

class ClassConstantModifier
{
    /**
     * @var \SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder
     */
    protected $classNodeFinder;

    /**
     * @param \SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder $classNodeFinder
     */
    public function __construct(ClassNodeFinder $classNodeFinder)
    {
        $this->classNodeFinder = $classNodeFinder;
    }

    /**
     * @param \Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param $value
     *
     * @return \Shared\Transfer\ClassInformationTransfer
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
