<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodChecker;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Builder\Visitor\AddClassToClassListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemoveClassFromClassListVisitor;
use SprykerSdk\Integrator\Helper\ClassHelper;

class ClassListModifier
{
    /**
     * @var \PhpParser\NodeTraverser
     */
    protected $nodeTraverser;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifier
     */
    protected $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder
     */
    protected $classNodeFinder;

    /**
     * @param \PhpParser\NodeTraverser $nodeTraverser
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifier $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder $classNodeFinder
     */
    public function __construct(
        NodeTraverser $nodeTraverser,
        CommonClassModifier $commonClassModifier,
        ClassNodeFinder $classNodeFinder
    ) {
        $this->nodeTraverser = $nodeTraverser;
        $this->commonClassModifier = $commonClassModifier;
        $this->classNodeFinder = $classNodeFinder;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $classNameToAdd
     * @param string $constantName
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassConstant(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $classNameToAdd,
        string $constantName
    ): ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            $classInformationTransfer = $this->commonClassModifier->overrideMethodFromParent($classInformationTransfer, $targetMethodName);
            $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        }

        $classMethodChecker = new ClassMethodChecker();
        if ($classMethodChecker->isMethodReturnArray($methodNode)) {
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor(new AddUseVisitor($classNameToAdd));
            $nodeTraverser->addVisitor(
                new AddClassToClassListVisitor(
                    $targetMethodName,
                    $classNameToAdd,
                    $constantName
                )
            );

            $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

            return $classInformationTransfer;
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new AddUseVisitor($classNameToAdd));
        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        $classHelper = new ClassHelper();
        $methodBody = [new Return_((new BuilderFactory())->classConstFetch($classHelper->getShortClassName($classNameToAdd), $constantName))];

        $this->commonClassModifier->replaceMethodBody($classInformationTransfer, $targetMethodName, $methodBody);

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $classNameToRemove
     * @param string $targetMethodName
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function unwireClassConstant(
        ClassInformationTransfer $classInformationTransfer,
        string $classNameToRemove,
        string $targetMethodName
    ): ?ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            return null;
        }

        if (!(new ClassMethodChecker())->isMethodReturnArray($methodNode)) {
            return $this->commonClassModifier->removeClassMethod($classInformationTransfer, $targetMethodName);
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(
            new RemoveClassFromClassListVisitor(
                $targetMethodName,
                $classNameToRemove
            )
        );

        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }
}
