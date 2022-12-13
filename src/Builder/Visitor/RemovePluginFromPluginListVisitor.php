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
     * @var string
     */
    protected $targetMethodName;

    /**
     * @var string
     */
    protected $classNameToRemove;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @param string $targetMethodName
     * @param string $classNameToRemove
     */
    public function __construct(string $targetMethodName, string $classNameToRemove)
    {
        $this->targetMethodName = ltrim($targetMethodName, '\\');
        $this->classNameToRemove = ltrim($classNameToRemove, '\\');
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function enterNode(Node $node): Node
    {
        if ($node->getType() === static::STATEMENT_CLASS_METHOD && $node->name->toString() === $this->targetMethodName) {
            $this->methodFound = true;

            $node = $this->filterStatements($node);
        }

        if ($this->methodFound && $node->getType() === static::STATEMENT_ARRAY) {
            $arrayItemsCount = count($node->items);
            $node = $this->removePluginFromArrayNode($node);
            if ($arrayItemsCount !== count($node->items)) {
                $this->methodFound = false;
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function filterStatements(Node $node): Node
    {
        $pluginToRemoveIndex = null;
        foreach ($node->stmts as $index => $stmt) {
            if ($stmt instanceof If_) {
                $stmt = $this->filterStatements($stmt);
            }

            if (
                $stmt instanceof Expression
                && $stmt->expr instanceof Assign
                && $stmt->expr->expr instanceof New_
                && $stmt->expr->expr->class->toString() === $this->classNameToRemove
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
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    protected function removePluginFromArrayNode(Node $node): Node
    {
        $items = [];
        foreach ($node->items as $item) {
            $nodeClassName = $item->value->class->toString();
            if ($nodeClassName !== $this->classNameToRemove) {
                $items[] = $item;
            }
        }

        $node->items = $items;

        return $node;
    }
}
