<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Business\Builder\ClassModifier;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Business\Builder\Checker\ClassMethodChecker;
use SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Business\Builder\Visitor\AddPluginToPluginListVisitor;
use SprykerSdk\Integrator\Business\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Business\Builder\Visitor\RemovePluginFromPluginListVisitor;
use SprykerSdk\Integrator\Business\Helper\ClassHelper;
use SprykerSdk\Shared\Transfer\ClassInformationTransfer;

class ClassInstanceClassModifier
{
    /**
     * @var \PhpParser\NodeTraverser
     */
    protected $nodeTraverser;

    /**
     * @var \SprykerSdk\Integrator\Business\Builder\ClassModifier\CommonClassModifier
     */
    protected $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder
     */
    protected $classNodeFinder;

    /**
     * @param \PhpParser\NodeTraverser $nodeTraverser
     * @param \SprykerSdk\Integrator\Business\Builder\ClassModifier\CommonClassModifier $commonClassModifier
     * @param \SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder $classNodeFinder
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
     * @param \SprykerSdk\Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $classNameToAdd
     * @param string|null $before
     * @param string|null $after
     *
     * @return \SprykerSdk\Shared\Transfer\ClassInformationTransfer
     */
    public function wireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $classNameToAdd,
        string $before = '',
        string $after = ''
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
                new AddPluginToPluginListVisitor(
                    $targetMethodName,
                    $classNameToAdd,
                    $before,
                    $after
                )
            );

            $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

            return $classInformationTransfer;
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new AddUseVisitor($classNameToAdd));
        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        $classHelper = new ClassHelper();
        $methodBody = [new Return_((new BuilderFactory())->new($classHelper->getShortClassName($classNameToAdd)))];
        $this->commonClassModifier->replaceMethodBody($classInformationTransfer, $targetMethodName, $methodBody);

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $classNameToRemove
     * @param string $targetMethodName
     *
     * @return \SprykerSdk\Shared\Transfer\ClassInformationTransfer|null
     */
    public function unwireClassInstance(
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
            new RemovePluginFromPluginListVisitor(
                $targetMethodName,
                $classNameToRemove
            )
        );

        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }
}
