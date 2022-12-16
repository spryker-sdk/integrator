<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class RemovePluginFromPluginListVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const STATEMENT_ARRAY = 'Expr_Array';

    /**
     * @var string
     */
    protected const STATEMENT_ASSIGN = 'Expr_Assign';

    /**
     * @var string
     */
    protected const STATEMENT_CLASS_METHOD = 'Stmt_ClassMethod';

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected ClassMetadataTransfer $classMetadataTransfer;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @var \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface
     */
    protected ArgumentBuilderInterface $argumentBuilder;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface $argumentBuilder
     */
    public function __construct(ClassMetadataTransfer $classMetadataTransfer, ArgumentBuilderInterface $argumentBuilder)
    {
        $this->classMetadataTransfer = $classMetadataTransfer;
        $this->argumentBuilder = $argumentBuilder;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function enterNode(Node $node): Node
    {
        $targetMethodName = ltrim($this->classMetadataTransfer->getTargetMethodNameOrFail(), '\\');
        $classNameToRemove = ltrim($this->classMetadataTransfer->getSourceOrFail(), '\\');

        if ($node->getType() === static::STATEMENT_CLASS_METHOD && $node->name->toString() === $targetMethodName) {
            $this->methodFound = true;

            $node = $this->filterStatements($node, $classNameToRemove);
        }

        if ($this->methodFound && $node->getType() === static::STATEMENT_ARRAY) {
            $arrayItemsCount = count($node->items);
            $node = $this->removePluginFromArrayNode($node, $classNameToRemove);
            if ($arrayItemsCount !== count($node->items)) {
                $this->methodFound = false;
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     * @param string $classNameToRemove
     *
     * @return \PhpParser\Node
     */
    public function filterStatements(Node $node, string $classNameToRemove): Node
    {
        $pluginToRemoveIndex = null;
        foreach ($node->stmts as $index => $stmt) {
            if ($stmt instanceof If_) {
                $stmt = $this->filterStatements($stmt, $classNameToRemove);
                if (!$stmt->stmts) {
                    $pluginToRemoveIndex = $index;

                    break;
                }
            }

            if (
                $stmt instanceof Expression
                && $stmt->expr instanceof Assign
                && $stmt->expr->expr instanceof New_
                && $stmt->expr->expr->class->toString() === $classNameToRemove
                && (
                    !$this->classMetadataTransfer->getConstructorArguments()->count()
                    || $this->isArgumentCorrect($stmt->expr->expr->args)
                )
            ) {
                $pluginToRemoveIndex = $index;

                break;
            }
        }

        if ($pluginToRemoveIndex !== null) {
            array_splice($node->stmts, $pluginToRemoveIndex, 1);
            $this->methodFound = false;
        }

        return $node;
    }

    /**
     * @param array<\PhpParser\Node\Arg|\PhpParser\Node\VariadicPlaceholder> $args
     *
     * @return bool
     */
    protected function isArgumentCorrect(array $args): bool
    {
        $standard = new Standard();
        $arguments = $this->argumentBuilder->getValueArguments($this->classMetadataTransfer->getConstructorArguments());
        foreach ($args as $index => $arg) {
            if ($standard->prettyPrintExpr($arg->value) !== $arguments[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \PhpParser\Node $node
     * @param string $classNameToRemove
     *
     * @return \PhpParser\Node
     */
    protected function removePluginFromArrayNode(Node $node, string $classNameToRemove): Node
    {
        $items = [];
        foreach ($node->items as $item) {
            $nodeClassName = $item->value->class->toString();
            if ($nodeClassName !== $classNameToRemove) {
                $items[] = $item;
            }
        }

        $node->items = $items;

        return $node;
    }
}
