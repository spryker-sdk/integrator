<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class AddPluginToPluginListVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const ARRAY_MERGE_FUNCTION = 'array_merge';

    /**
     * @var string
     */
    protected const STATEMENT_ARRAY = 'Expr_Array';

    /**
     * @var string
     */
    protected const STATEMENT_CLASS_METHOD = 'Stmt_ClassMethod';

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected $classMetadataTransfer;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     */
    public function __construct(ClassMetadataTransfer $classMetadataTransfer)
    {
        $this->classMetadataTransfer = $classMetadataTransfer;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof ClassMethod && $node->name->toString() === $this->classMetadataTransfer->getTargetMethodNameOrFail()) {
            $this->methodFound = true;

            return $node;
        }

        if ($this->methodFound) {
            if ($node instanceof FuncCall && $this->isArrayMergeFuncCallNode($node)) {
                $this->addNewPluginIntoArrayMergeFuncNode($node);

                return $this->successfullyProcessed();
            }

            if ($node instanceof If_ && $this->classMetadataTransfer->getCondition() !== null) {
                $this->addNewPluginIntoIfCondition($node);

                return $this->successfullyProcessed();
            }

            if ($node instanceof Array_ && $this->classMetadataTransfer->getCondition() === null) {
                $this->addNewPlugin($node);

                return $this->successfullyProcessed();
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Stmt\If_ $ifCondition
     *
     * @return string
     */
    protected function getIfClausePrettyPrint(If_ $ifCondition): string
    {
        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintExpr($ifCondition->cond);
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return bool
     */
    protected function isArrayMergeFuncCallNode(FuncCall $node): bool
    {
        return $node->name instanceof Name && $node->name->parts[0] === static::ARRAY_MERGE_FUNCTION;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPluginIntoArrayMergeFuncNode(FuncCall $node): Node
    {
        if ($this->isPluginAddedInArrayMerge($node)) {
            return $node;
        }

        $node->args[] = new Arg($this->createArrayWithInstanceOf());

        return $node;
    }

    /**
     * @param \PhpParser\Node\Stmt\If_ $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPluginIntoIfCondition(If_ $node): Node
    {
        if ($this->getIfClausePrettyPrint($node) === $this->classMetadataTransfer->getCondition()) {
            $this->addNewPluginInstance($node);

            return $node;
        }

        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof If_) {
                return $this->addNewPluginIntoIfCondition($stmt);
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Stmt\If_ $statement
     *
     * @return void
     */
    protected function addNewPluginInstance(If_ $statement): void
    {
        foreach ($statement->stmts as $stmt) {
            if ($stmt instanceof Expression && $stmt->expr instanceof Assign) {
                array_unshift($statement->stmts, $this->createNewAssignStatement($stmt->expr));

                break;
            }
        }
    }

    /**
     * @param \PhpParser\Node\Expr\Assign $reference
     *
     * @return \PhpParser\Node\Stmt\Expression
     */
    protected function createNewAssignStatement(Assign $reference): Expression
    {
        return new Expression(new Assign($reference->var, (new BuilderFactory())->new(
            (new ClassHelper())->getShortClassName($this->classMetadataTransfer->getSourceOrFail()),
        )));
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return bool
     */
    protected function isPluginAddedInArrayMerge(FuncCall $node): bool
    {
        foreach ($node->getArgs() as $arg) {
            if (!$arg->value instanceof Array_) {
                continue;
            }

            if ($this->isPluginAdded($arg->value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \PhpParser\Node\Expr\Array_
     */
    protected function createArrayWithInstanceOf(): Array_
    {
        return new Array_(
            [$this->createArrayItemWithInstanceOf()],
        );
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPlugin(Array_ $node): Node
    {
        if ($this->isPluginAdded($node)) {
            return $node;
        }

        $items = [];
        $itemAdded = false;
        foreach ($node->items as $item) {
            if ($item === null || !($item->value instanceof New_)) {
                continue;
            }

            $nodeClassName = $item->value->class->toString();
            if ($nodeClassName === $this->classMetadataTransfer->getBeforeOrFail()) {
                $items[] = $this->createArrayItemWithInstanceOf();
                $items[] = $item;
                $itemAdded = true;

                continue;
            }

            if ($nodeClassName === $this->classMetadataTransfer->getAfterOrFail()) {
                $items[] = $item;
                $items[] = $this->createArrayItemWithInstanceOf();
                $itemAdded = true;

                continue;
            }

            $items[] = $item;
        }

        if (!$itemAdded) {
            $items[] = $this->createArrayItemWithInstanceOf();
        }

        $node->items = $items;

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     *
     * @return bool
     */
    protected function isPluginAdded(Array_ $node): bool
    {
        foreach ($node->items as $item) {
            if ($item === null || !($item->value instanceof New_)) {
                continue;
            }
            $nodeClassName = $item->value->class->toString();

            if ($this->isKeyEqualsToCurrentOne($item) && $nodeClassName === $this->classMetadataTransfer->getSourceOrFail()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem $node
     *
     * @return bool
     */
    protected function isKeyEqualsToCurrentOne(ArrayItem $node): bool
    {
        $nodeKey = $this->getArrayItemNodeKey($node);

        return ltrim((string)$nodeKey, '\\') === ltrim((string)$this->classMetadataTransfer->getIndex(), '\\');
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem $node
     *
     * @return string|null
     */
    protected function getArrayItemNodeKey(ArrayItem $node): ?string
    {
        if ($node->key === null) {
            return null;
        }

        if ($node->key instanceof ClassConstFetch && $node->key->class instanceof Name && $node->key->name instanceof Identifier) {
            return sprintf('%s::%s', $node->key->class, $node->key->name);
        }

        if ($node->key instanceof String_) {
            return $node->key->value;
        }

        return null;
    }

    /**
     * @return \PhpParser\Node\Expr\ArrayItem
     */
    protected function createArrayItemWithInstanceOf(): ArrayItem
    {
        return new ArrayItem(
            (new BuilderFactory())->new(
                (new ClassHelper())->getShortClassName($this->classMetadataTransfer->getSourceOrFail()),
            ),
            $this->classMetadataTransfer->getIndex() ? $this->createIndexExpr($this->classMetadataTransfer->getIndex()) : null,
        );
    }

    /**
     * @param string $index
     *
     * @return \PhpParser\Node\Expr
     */
    protected function createIndexExpr(string $index): Expr
    {
        if (strpos($index, 'static::') === 0) {
            $indexParts = explode('::', $index);

            return new ClassConstFetch(
                new Name('static'),
                $indexParts[1],
            );
        }

        if (strpos($index, '::') !== false) {
            $indexParts = explode('::', $index);
            $classNamespaceChain = explode('\\', $indexParts[0]);

            return new ClassConstFetch(
                new Name(end($classNamespaceChain)),
                $indexParts[1],
            );
        }

        return new String_($index);
    }

    /**
     * @return int
     */
    protected function successfullyProcessed(): int
    {
        $this->methodFound = false;

        return NodeTraverser::DONT_TRAVERSE_CHILDREN;
    }
}
