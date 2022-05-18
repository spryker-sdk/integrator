<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use SprykerSdk\Integrator\Builder\ClassModifier\AddVisitorsTrait;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddPluginToPluginListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemovePluginFromPluginListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemoveUseVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ClassInstanceReturnArrayModifierStrategy implements ClassInstanceModifierStrategyInterface
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
        if (
            $node->getReturnType()
            && $node->getReturnType() instanceof Identifier
            && $node->getReturnType()->name === 'array'
        ) {
            return true;
        }

        if (!$node->stmts) {
            return false;
        }

        $lastNode = end($node->stmts);

        if ($lastNode instanceof Return_ && $lastNode->expr instanceof Array_) {
            return true;
        }

        if (
            $lastNode instanceof Return_
            && $lastNode->expr instanceof FuncCall
            && strpos($lastNode->expr->name->toString(), 'array_') === 0
        ) {
            return true;
        }

        if ($lastNode instanceof Return_ && $lastNode->expr instanceof Variable) {
            $varName = $lastNode->expr->name;

            return (bool)(new NodeFinder())->findFirst($node->stmts, function (Node $node) use ($varName) {
                return $node instanceof Assign
                    && $node->var instanceof Variable
                    && $node->var->name === $varName
                    && $node->expr instanceof Array_;
            });
        }

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

        if ($classMetadataTransfer->getIndex() !== null && $this->isIndexFullyQualifiedClassName($classMetadataTransfer->getIndex())) {
            $visitors[] = new AddUseVisitor($this->getFullyQualifiedClassNameFromIndex($classMetadataTransfer->getIndex()));
        }


        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
    }

    /**
     * @param string $index
     *
     * @return bool
     */
    protected function isIndexFullyQualifiedClassName(string $index): bool
    {
        return strpos($index, '::') !== false && strpos($index, 'static::') === false;
    }

    /**
     * @param string $index
     *
     * @return string
     */
    protected function getFullyQualifiedClassNameFromIndex(string $index): string
    {
        return explode('::', $index)[0];
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

        return $this->addVisitorsClassInformationTransfer($classInformationTransfer, $visitors);
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
            new AddPluginToPluginListVisitor(
                $targetMethodName,
                $classMetadataTransfer,
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
//            new RemoveUseVisitor($classMetadataTransfer->getSourceOrFail()),
            new RemovePluginFromPluginListVisitor($targetMethodName, $classMetadataTransfer->getSourceOrFail()),
        ];
    }
}
