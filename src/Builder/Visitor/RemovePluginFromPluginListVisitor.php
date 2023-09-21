<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeTraverser;
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
     * @var bool
     */
    protected $cleanUpMethod = false;

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

        if ($this->methodFound && $node instanceof Array_) {
            $node = $this->removePluginFromArrayNode($node, $classNameToRemove);
            if (!$node->items) {
                $this->cleanUpMethod = true;
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|array<\PhpParser\Node>|int|null
     */
    public function leaveNode(Node $node)
    {
        if (!$this->methodFound || $node->getType() !== static::STATEMENT_CLASS_METHOD) {
            return $node;
        }

        $this->methodFound = false;

        if ($this->cleanUpMethod) {
            $this->cleanUpMethod = false;

            return NodeTraverser::REMOVE_NODE;
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
        $arguments = $this->classMetadataTransfer->getConstructorArguments()->getArrayCopy();
        foreach ($args as $index => $arg) {
            $argument = $arguments[$index] ?? null;
            if (!$argument) {
                continue;
            }
            $argumentValue = json_decode($argument->getValue());
            if ($standard->prettyPrintExpr($arg->value) !== $argumentValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     * @param string $classNameToRemove
     *
     * @throws \InvalidArgumentException
     *
     * @return \PhpParser\Node\Expr\Array_
     */
    protected function removePluginFromArrayNode(Array_ $node, string $classNameToRemove): Array_
    {
        $items = [];
        foreach ($node->items as $item) {
            if ($item === null) {
                continue;
            }

            if ($item->value instanceof Array_) {
                $subArrayNode = $this->removePluginFromArrayNode($item->value, $classNameToRemove);

                if (!$subArrayNode->items) {
                    continue;
                }

                $items[] = new ArrayItem($subArrayNode, $item->key);

                continue;
            }

            if ($item->value instanceof New_) {
                if ($item->value->class->toString() !== $classNameToRemove) {
                    $items[] = $item;
                }

                continue;
            }

            throw new InvalidArgumentException(sprintf('Unsupported un-wire type `%s`', get_class($item->value)));
        }

        $node->items = $items;

        return $node;
    }
}
