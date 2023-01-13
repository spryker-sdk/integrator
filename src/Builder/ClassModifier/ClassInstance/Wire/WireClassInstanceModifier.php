<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstance\Wire;

use PhpParser\Node\Stmt\ClassMethod;
use RuntimeException;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class WireClassInstanceModifier implements WireClassInstanceModifierInterface
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
     * @var array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface>
     */
    protected $classInstanceModifierStrategies;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface $classMethodChecker
     * @param array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire\WireClassInstanceModifierStrategyInterface> $classInstanceModifierStrategies
     */
    public function __construct(
        CommonClassModifierInterface $commonClassModifier,
        ClassNodeFinderInterface $classNodeFinder,
        ClassMethodCheckerInterface $classMethodChecker,
        array $classInstanceModifierStrategies
    ) {
        $this->commonClassModifier = $commonClassModifier;
        $this->classNodeFinder = $classNodeFinder;
        $this->classMethodChecker = $classMethodChecker;
        $this->classInstanceModifierStrategies = $classInstanceModifierStrategies;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wire(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        $methodNode = $this->getMethodNode($classInformationTransfer, $classMetadataTransfer);
        foreach ($this->classInstanceModifierStrategies as $classInstanceModifierStrategy) {
            if ($classInstanceModifierStrategy->isApplicable($methodNode)) {
                return $classInstanceModifierStrategy->wireClassInstance($classInformationTransfer, $classMetadataTransfer);
            }
        }

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @throws \RuntimeException
     *
     * @return \PhpParser\Node\Stmt\ClassMethod
     */
    protected function getMethodNode(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassMethod {
        $targetMethodName = $classMetadataTransfer->getTargetMethodNameOrFail();
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if ($methodNode) {
            return $methodNode;
        }

        $classInformationTransfer = $this->commonClassModifier->overrideMethodFromParent($classInformationTransfer, $targetMethodName);
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if ($methodNode) {
            return $methodNode;
        }

        $classInformationTransfer = $this->commonClassModifier->createClassMethod(
            $classInformationTransfer,
            $targetMethodName,
            [],
            true,
            '',
        );

        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if ($methodNode) {
            return $methodNode;
        }

        throw new RuntimeException('Method node not found');
    }
}
