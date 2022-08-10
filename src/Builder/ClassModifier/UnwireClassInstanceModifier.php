<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class UnwireClassInstanceModifier implements UnwireClassInstanceModifierInterface
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
     * @var array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterfaceCheck>
     */
    protected $classInstanceModifierStrategies;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface $classMethodChecker
     * @param array<\SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire\UnwireClassInstanceModifierStrategyInterfaceCheck> $classInstanceModifierStrategies
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
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function unwireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ?ClassInformationTransfer {
        $targetMethodName = $classMetadataTransfer->getTargetMethodNameOrFail();
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            return null;
        }

        foreach ($this->classInstanceModifierStrategies as $classInstanceModifierStrategy) {
            if ($classInstanceModifierStrategy->isApplicable($methodNode)) {
                return $classInstanceModifierStrategy->unwireClassInstance($classInformationTransfer, $classMetadataTransfer);
            }
        }

        return $classInformationTransfer;
    }
}
