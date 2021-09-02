<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Business\Builder\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class RemovePluginFromPluginListVisitor extends NodeVisitorAbstract
{
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
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if ($node->getType() === 'Stmt_ClassMethod' && $node->name->toString() === $this->targetMethodName) {
            $this->methodFound = true;

            return $node;
        }

        if ($this->methodFound && $node->getType() === 'Expr_Array') {
            $node = $this->removePlugin($node);
            $this->methodFound = false;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    protected function removePlugin(Node $node): Node
    {
        $items = [];
        /** @var \PhpParser\Node\Expr\Array_ $node */
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
