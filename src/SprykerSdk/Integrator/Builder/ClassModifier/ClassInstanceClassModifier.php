<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Return_;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Builder\Visitor\AddPluginToPluginListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemovePluginFromPluginListVisitor;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassInstanceClassModifier implements ClassInstanceClassModifierInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifier
     */
    protected $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder
     */
    protected $classNodeFinder;

    /**
     * @var \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface
     */
    protected $classMethodChecker;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifier $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder $classNodeFinder
     * @param \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface $classMethodChecker
     */
    public function __construct(
        CommonClassModifier $commonClassModifier,
        ClassNodeFinder $classNodeFinder,
        ClassMethodCheckerInterface $classMethodChecker
    ) {
        $this->commonClassModifier = $commonClassModifier;
        $this->classNodeFinder = $classNodeFinder;
        $this->classMethodChecker = $classMethodChecker;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $classNameToAdd
     * @param string $before
     * @param string $after
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
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

        if ($this->classMethodChecker->isMethodReturnArray($methodNode)) {
            $visitors = [
                new AddUseVisitor($classNameToAdd),
                new AddPluginToPluginListVisitor(
                    $targetMethodName,
                    $classNameToAdd,
                    $before,
                    $after
                ),
            ];

            return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
        }

        $visitors = [
            new AddUseVisitor($classNameToAdd),
        ];
        $classInformationTransfer = $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);

        $classHelper = new ClassHelper();
        $methodBody = [new Return_((new BuilderFactory())->new($classHelper->getShortClassName($classNameToAdd)))];
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
    public function unwireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        string $classNameToRemove,
        string $targetMethodName
    ): ?ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            return null;
        }

        if (!$this->classMethodChecker->isMethodReturnArray($methodNode)) {
            return $this->commonClassModifier->removeClassMethod($classInformationTransfer, $targetMethodName);
        }

        $visitors = [
            new RemovePluginFromPluginListVisitor($targetMethodName, $classNameToRemove),
        ];

        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
    }
}
