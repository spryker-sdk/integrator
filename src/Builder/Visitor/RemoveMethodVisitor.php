<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;

class RemoveMethodVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected $methodNameToRemove;

    /**
     * @param string $methodNameToRemove
     */
    public function __construct(string $methodNameToRemove)
    {
        $this->methodNameToRemove = $methodNameToRemove;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if (!($node instanceof Class_)) {
            return $node;
        }

        $stmts = [];
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $stmt->name->toString() === $this->methodNameToRemove) {
                continue;
            }

            $stmts[] = $stmt;
        }
        $node->stmts = $stmts;

        return $node;
    }
}
