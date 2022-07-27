<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\ReplaceNodePropertiesByNameVisitor;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ClassInstanceReturnClassModifierStrategy implements ClassInstanceModifierStrategyInterface
{
    use AddVisitorsTrait;

    /**
     * @var array<string>
     */
    protected const AVAILABLE_NODE_SUFFIXES = ['plugin', 'subscriber', 'widget'];

    /**
     * @var array<string>
     */
    protected const FORBIDDEN_NODE_SUFFIXES = ['\container', 'collection'];

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
        if (
            !$node->getReturnType() instanceof Node
        ) {
            return false;
        }

        $returnType = $node->getReturnType()->toString();

        foreach (static::FORBIDDEN_NODE_SUFFIXES as $pattern) {
            if (strpos(strtolower($returnType), $pattern)) {
                return false;
            }
        }

        foreach (static::AVAILABLE_NODE_SUFFIXES as $pattern) {
            if (strpos(strtolower($returnType), $pattern)) {
                return true;
            }
        }

        return false;
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
        $visitors = $this->getWireVisitors($classMetadataTransfer);

        $classInformationTransfer = $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);

        $classHelper = new ClassHelper();
        $shortClassName = $classHelper->getShortClassName($classMetadataTransfer->getSourceOrFail());

        $methodBody = [new Return_((new BuilderFactory())->new($shortClassName))];

        $methodNodeProperties = [
            ReplaceNodePropertiesByNameVisitor::STMTS => $methodBody,
            ReplaceNodePropertiesByNameVisitor::RETURN_TYPE => new Identifier($shortClassName),
        ];
        $this->commonClassModifier->replaceMethodBody(
            $classInformationTransfer,
            $classMetadataTransfer->getTargetMethodNameOrFail(),
            $methodNodeProperties,
        );

        return $classInformationTransfer;
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

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\NodeVisitorAbstract>
     */
    protected function getWireVisitors(ClassMetadataTransfer $classMetadataTransfer): array
    {
        return [
            new AddUseVisitor($classMetadataTransfer->getSourceOrFail()),
        ];
    }
}
