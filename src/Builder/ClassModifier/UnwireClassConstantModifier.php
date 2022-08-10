<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Builder\Visitor\RemoveClassFromClassListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemoveUseVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class UnwireClassConstantModifier implements UnwireClassConstantModifierInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface
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
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface $commonClassModifier
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

        if (!$this->classMethodChecker->isMethodReturnArray($methodNode)) {
            return $this->commonClassModifier->removeClassMethod($classInformationTransfer, $targetMethodName);
        }

        $visitors = [
            new RemoveUseVisitor($classNameToRemove),
            new RemoveClassFromClassListVisitor($targetMethodName, $classNameToRemove),
        ];

        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
    }
}
