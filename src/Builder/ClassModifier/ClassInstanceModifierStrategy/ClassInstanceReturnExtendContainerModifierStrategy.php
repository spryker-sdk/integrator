<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddPluginToPluginCollectionExtendContainerVisitor;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemovePluginFromPluginCollectionExtendContainerVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ClassInstanceReturnExtendContainerModifierStrategy implements ClassInstanceModifierStrategyInterface
{
    use AddVisitorsTrait;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface
     */
    protected $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface
     */
    protected $argumentBuilder;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface $argumentBuilder
     */
    public function __construct(CommonClassModifierInterface $commonClassModifier, ArgumentBuilderInterface $argumentBuilder)
    {
        $this->commonClassModifier = $commonClassModifier;
        $this->argumentBuilder = $argumentBuilder;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isApplicable(ClassMethod $node): bool
    {
        if (
            $node->getReturnType() instanceof Identifier
            && $node->getReturnType()->toString() !== 'Container'
        ) {
            return false;
        }

        if (!$node->stmts) {
            return false;
        }

        $containerExtendCallExists = false;

        foreach ($node->stmts as $stmt) {
            if (
                $stmt instanceof Expression
                && $stmt->expr instanceof MethodCall
                && $stmt->expr->var instanceof Variable
                && $stmt->expr->var->name === 'container'
                && $stmt->expr->name instanceof Identifier
                && $stmt->expr->name->name === 'extend'
            ) {
                $containerExtendCallExists = true;
            }
        }

        $addPluginCallsExist = (bool)(new NodeFinder())->findFirst($node->stmts, function (Node $node) {
            return $node instanceof MethodCall
                && strpos(strtolower($node->name->toString()), 'add') !== false;
        });

        $addPluginChainedCallsExist = (bool)(new NodeFinder())->findFirst($node->stmts, function (Node $node) {
            return $node instanceof MethodCall
                && strpos(strtolower($node->name->toString()), 'add') !== false
                && $node->var instanceof MethodCall === true;
        });

        return $containerExtendCallExists && $addPluginCallsExist && !$addPluginChainedCallsExist;
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
        $visitors = $this->getUnwireVisitors($targetMethodName, $classMetadataTransfer);

        $classInformationTransfer = $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);

        return $classInformationTransfer;
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
            new AddPluginToPluginCollectionExtendContainerVisitor(
                $targetMethodName,
                $classMetadataTransfer,
                $this->argumentBuilder,
            ),
        ];
    }

    /**
     * @param string $targetMethodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return array<\PhpParser\NodeVisitorAbstract>
     */
    protected function getUnwireVisitors(string $targetMethodName, ClassMetadataTransfer $classMetadataTransfer): array
    {
        return [
            new RemovePluginFromPluginCollectionExtendContainerVisitor(
                $targetMethodName,
                $classMetadataTransfer,
            ),
        ];
    }
}