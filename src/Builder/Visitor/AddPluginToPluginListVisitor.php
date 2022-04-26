<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Name as NodeName;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Arg;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Helper\ClassHelper;
use PhpParser\Node\Expr\ArrayItem as ArrayItemNode;
use PhpParser\Node\Expr\Array_ as ArrayNode;
use PhpParser\Node\Expr\FuncCall as FuncCallNode;
use PhpParser\Node\Stmt\ClassMethod as ClassMethodNode;
use PhpParser\NodeTraverser;

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
     * @var string
     */
    protected $methodName;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $before;

    /**
     * @var string
     */
    protected $after;

    /**
     * @var string|null
     */
    protected $index;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @param string $methodName
     * @param string $className
     * @param string $before
     * @param string $after
     * @param string|null $index
     */
    public function __construct(string $methodName, string $className, string $before = '', string $after = '', ?string $index = null)
    {
        $this->methodName = $methodName;
        $this->className = ltrim($className, '\\');
        $this->before = ltrim($before, '\\');
        $this->after = ltrim($after, '\\');
        $this->index = $index;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof ClassMethodNode && $node->name->toString() === $this->methodName) {
            $this->methodFound = true;

            return $node;
        }

        if ($this->methodFound) {
            if ($node instanceof FuncCallNode && $this->isArrayMergeFuncCallNode($node)) {
                $this->addNewPluginIntoArrayMergeFuncNode($node);

                return $this->successfullyProcessed();
            }

            if ($node instanceof ArrayNode) {
                $this->addNewPlugin($node);

                return $this->successfullyProcessed();
            }
        }

        return $node;
    }

    /**
     * @param FuncCallNode $node
     *
     * @return bool
     */
    protected function isArrayMergeFuncCallNode(FuncCallNode $node): bool
    {
        return $node->name instanceof NodeName && $node->name->parts[0] === static::ARRAY_MERGE_FUNCTION;
    }

    /**
     * @param FuncCallNode $node
     *
     * @return Node
     */
    protected function addNewPluginIntoArrayMergeFuncNode(FuncCallNode $node): Node
    {
        if ($this->isPluginAddedInArrayMerge($node)) {
            return $node;
        }

        $node->args[] = new Arg($this->createArrayWithInstanceOf());

        return $node;
    }

    /**
     * @param FuncCallNode $node
     *
     * @return bool
     */
    protected function isPluginAddedInArrayMerge(FuncCallNode $node): bool
    {
        foreach ($node->getArgs() as $arg) {
            if (!$arg->value instanceof ArrayNode) {
                continue;
            }

            if ($this->isPluginAdded($arg->value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ArrayNode
     */
    protected function createArrayWithInstanceOf(): ArrayNode
    {
        return new ArrayNode(
            [$this->createArrayItemWithInstanceOf()]
        );
    }

    /**
     * @param ArrayNode $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPlugin(ArrayNode $node): Node
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
            if ($nodeClassName === $this->before) {
                $items[] = $this->createArrayItemWithInstanceOf();
                $items[] = $item;
                $itemAdded = true;

                continue;
            }
            if ($nodeClassName === $this->after) {
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
     * @param ArrayNode $node
     *
     * @return bool
     */
    protected function isPluginAdded(ArrayNode $node): bool
    {
        foreach ($node->items as $item) {
            if ($item === null || !($item->value instanceof New_)) {
                continue;
            }
            $nodeClassName = $item->value->class->toString();
            if ($nodeClassName === $this->className) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ArrayItemNode
     */
    protected function createArrayItemWithInstanceOf(): ArrayItemNode
    {
        return new ArrayItemNode(
            (new BuilderFactory())->new(
                (new ClassHelper())->getShortClassName($this->className),
            ),
            $this->index ? $this->createIndexExpr($this->index) : null,
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
