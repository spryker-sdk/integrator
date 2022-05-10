<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ClassInstanceReturnClassModifierStrategy implements ClassInstanceModifierStrategyInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface
     */
    protected $commonClassModifier;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface $commonClassModifier
     */
    public function __construct(CommonClassModifierInterface $commonClassModifier)
    {
        $this->commonClassModifier = $commonClassModifier;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isApplicable(ClassMethod $node): bool
    {
        //TODO: determine usecases; add tests
        return false;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function wireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        $visitors = $this->getWireVisitors($targetMethodName, $classMetadataTransfer);

        $classInformationTransfer = $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);

        $classHelper = new ClassHelper();
        $methodBody = [new Return_((new BuilderFactory())->new($classHelper->getShortClassName($classMetadataTransfer->getSourceOrFail())))];
        $this->commonClassModifier->replaceMethodBody($classInformationTransfer, $targetMethodName, $methodBody);

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function unwireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer {
        return $this->commonClassModifier->removeClassMethod($classInformationTransfer, $targetMethodName);
    }

    /**
     * @param string $targetMethodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\NodeVisitorAbstract>
     */
    protected function getWireVisitors(string $targetMethodName, ClassMetadataTransfer $classMetadataTransfer): array
    {
        return [
            new AddUseVisitor($classMetadataTransfer->getSourceOrFail()),
        ];
    }
}
