<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\BuilderFactory;
use PhpParser\Node\Name as NodeName;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem as ArrayItemNode;
use PhpParser\Node\Expr\Array_ as ArrayNode;
use PhpParser\Node\Expr\FuncCall as FuncCallNode;
use PhpParser\Node\Stmt\ClassMethod as ClassMethodNode;
use PhpParser\Node\Expr\ClassConstFetch as ClassConstFetchNode;
use PhpParser\Node\Arg;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Helper\ClassHelper;

class AddClassToClassListVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const ARRAY_MERGE_FUNCTION = 'array_merge';

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @var string
     */
    protected $constantName;

    /**
     * @var string
     */
    protected $before;

    /**
     * @var string
     */
    protected $after;

    /**
     * @var Node
     */
    protected $parentNode;

    /**
     * @param string $methodName
     * @param string $className
     * @param string $constantName
     * @param string $before
     * @param string $after
     */
    public function __construct(
        string $methodName,
        string $className,
        string $constantName,
        string $before = '',
        string $after = ''
    ) {
        $this->methodName = $methodName;
        $this->className = ltrim($className, '\\');
        $this->constantName = $constantName;
        $this->before = ltrim($before, '\\');
        $this->after = ltrim($after, '\\');
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof ClassMethodNode && $node->name->toString() === $this->methodName) {
            $this->methodFound = true;

            return $node;
        }

        if ($this->methodFound) {
            if ($node instanceof FuncCallNode && $this->isArrayMergeFuncCallNode($node)) {
                $this->addClassIntoArrayMergeFuncNode($node);

                return $this->successfullyProcessed();
            }

            if ($node instanceof ArrayNode) {
                $this->addClassIntoArrayNode($node);

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
    protected function addClassIntoArrayMergeFuncNode(FuncCallNode $node): Node
    {
        if ($this->isClassAddedInArrayMerge($node)) {
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
    protected function isClassAddedInArrayMerge(FuncCallNode $node): bool
    {
        foreach ($node->getArgs() as $arg) {
            if (!$arg->value instanceof ArrayNode) {
                continue;
            }

            if ($this->isClassInArrayNode($arg->value)) {
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
     * @return ArrayNode
     */
    protected function addClassIntoArrayNode(ArrayNode $node): ArrayNode
    {
        if ($this->isClassInArrayNode($node)) {
            return $node;
        }

        if ($this->before || $this->after) {
            $node->items = $this->addArrayElementToPosition($node);
        } else {
            $node->items[] = $this->createArrayItemWithInstanceOf();
        }

        return $node;
    }

    /**
     * @param ArrayNode $node
     *
     * @return bool
     */
    protected function isClassInArrayNode(ArrayNode $node): bool
    {
        foreach ($node->items as $item) {
            if ($item === null || !$item->value instanceof ClassConstFetchNode) {
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
     * @param \PhpParser\Node $node
     *
     * @return array
     */
    protected function addArrayElementToPosition(Node $node): array
    {
        $items = [];
        $itemAdded = false;

        foreach ($node->items as $item) {
            $nodeValue = sprintf('%s::%s', $item->value->class->toString(), $item->value->name->toString());
            if ($nodeValue === $this->before && !$itemAdded) {
                $items[] = $this->createArrayItemWithInstanceOf();
                $items[] = $item;
                $itemAdded = true;

                continue;
            }
            if ($nodeValue === $this->after && !$itemAdded) {
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

        return $items;
    }

    /**
     * @return ArrayItemNode
     */
    protected function createArrayItemWithInstanceOf(): ArrayItemNode
    {
        return new ArrayItemNode(
            (new BuilderFactory())->classConstFetch(
                (new ClassHelper())->getShortClassName($this->className),
                $this->constantName,
            ),
        );
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
