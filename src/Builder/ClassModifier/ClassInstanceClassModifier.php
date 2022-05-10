<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use RuntimeException;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ClassInstanceClassModifier implements ClassInstanceClassModifierInterface
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
     * @var array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\ClassInstanceModifierStrategyInterface>
     */
    protected $classInstanceModifierStrategies;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface $classMethodChecker
     * @param array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\ClassInstanceModifierStrategyInterface> $classInstanceModifierStrategies
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
     * @param string $targetMethodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            $classInformationTransfer = $this->commonClassModifier->overrideMethodFromParent($classInformationTransfer, $targetMethodName);
            $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        }

        if ($methodNode === null) {
            throw new RuntimeException('Method node not found');
        }

        foreach ($this->classInstanceModifierStrategies as $classInstanceModifierStrategy) {
            if ($classInstanceModifierStrategy->isApplicable($methodNode)) {
                return $classInstanceModifierStrategy->wireClassInstance($classInformationTransfer, $targetMethodName, $classMetadataTransfer);
            }
        }

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function unwireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        ClassMetadataTransfer $classMetadataTransfer
    ): ?ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            return null;
        }

        foreach ($this->classInstanceModifierStrategies as $classInstanceModifierStrategy) {
            if ($classInstanceModifierStrategy->isApplicable($methodNode)) {
                return $classInstanceModifierStrategy->unwireClassInstance($classInformationTransfer, $targetMethodName, $classMetadataTransfer);
            }
        }

        return $classInformationTransfer;
    }
}
