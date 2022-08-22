<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassConstant\Wire;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Return_;
use RuntimeException;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddClassToClassListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\ReplaceNodePropertiesByNameVisitor;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class WireModifier implements WireModifierInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface
     */
    protected $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    protected $classNodeFinder;

    /**
     * @var \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface
     */
    protected $classMethodChecker;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface $classMethodChecker
     */
    public function __construct(
        CommonClassModifierInterface $commonClassModifier,
        ClassNodeFinderInterface $classNodeFinder,
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
     * @param string $constantName
     * @param string $before
     * @param string $after
     *
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassConstant(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $classNameToAdd,
        string $constantName,
        string $before = '',
        string $after = ''
    ): ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            $classInformationTransfer = $this->commonClassModifier
                ->overrideMethodFromParent($classInformationTransfer, $targetMethodName);
            $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        }

        if ($methodNode === null) {
            throw new RuntimeException('Method node not found');
        }

        if ($this->classMethodChecker->isMethodReturnArray($methodNode)) {
            $visitors = [
                new AddUseVisitor($classNameToAdd),
                new AddClassToClassListVisitor(
                    $targetMethodName,
                    $classNameToAdd,
                    $constantName,
                    $before,
                    $after,
                ),
            ];

            return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
        }

        $visitors = [
            new AddUseVisitor($classNameToAdd),
        ];
        $classInformationTransfer = $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);

        $classHelper = new ClassHelper();
        $methodBody = [new Return_((new BuilderFactory())->classConstFetch($classHelper->getShortClassName($classNameToAdd), $constantName))];

        $methodNodeProperties = [ReplaceNodePropertiesByNameVisitor::STMTS => $methodBody];
        $this->commonClassModifier->replaceMethodBody($classInformationTransfer, $targetMethodName, $methodNodeProperties);

        return $classInformationTransfer;
    }
}
