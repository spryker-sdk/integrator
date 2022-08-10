<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire;

use PhpParser\Node\Stmt\ClassMethod;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\ApplicableCheck\CheckApplicableInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class UnwireClassInstanceReturnClassModifierStrategy implements UnwireClassInstanceModifierStrategyInterfaceCheck
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface
     */
    protected CommonClassModifierInterface $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\ApplicableCheck\CheckApplicableInterface
     */
    protected CheckApplicableInterface $applicableCheck;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\ApplicableCheck\CheckApplicableInterface $applicableCheck
     */
    public function __construct(CommonClassModifierInterface $commonClassModifier, CheckApplicableInterface $applicableCheck)
    {
        $this->commonClassModifier = $commonClassModifier;
        $this->applicableCheck = $applicableCheck;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isApplicable(ClassMethod $node): bool
    {
        return $this->applicableCheck->isApplicable($node);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function unwireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        return $this->commonClassModifier->removeClassMethod(
            $classInformationTransfer,
            $classMetadataTransfer->getTargetMethodNameOrFail(),
        );
    }
}
