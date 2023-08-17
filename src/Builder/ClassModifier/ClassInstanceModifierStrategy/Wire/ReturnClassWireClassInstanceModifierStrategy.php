<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Wire;

use PhpParser\BuilderFactory;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableModifierStrategyInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\Visitor\ReplaceNodePropertiesByNameVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ReturnClassWireClassInstanceModifierStrategy implements WireClassInstanceModifierStrategyInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface
     */
    protected CommonClassModifierInterface $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableModifierStrategyInterface
     */
    protected ApplicableModifierStrategyInterface $applicableCheck;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableModifierStrategyInterface $applicableCheck
     */
    public function __construct(CommonClassModifierInterface $commonClassModifier, ApplicableModifierStrategyInterface $applicableCheck)
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
    public function wireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        $classInformationTransfer = $this->addVisitorsClassInformationTransfer($classInformationTransfer, []);

        $className = $classMetadataTransfer->getSourceOrFail();

        $methodBody = [new Return_((new BuilderFactory())->new($className))];

        $methodNodeProperties = [
            ReplaceNodePropertiesByNameVisitor::STMTS => $methodBody,
            ReplaceNodePropertiesByNameVisitor::RETURN_TYPE => new Identifier($className),
        ];
        $this->commonClassModifier->replaceMethodBody(
            $classInformationTransfer,
            $classMetadataTransfer->getTargetMethodNameOrFail(),
            $methodNodeProperties,
        );

        return $classInformationTransfer;
    }
}
