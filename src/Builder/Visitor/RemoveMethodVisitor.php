<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;

class RemoveMethodVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const STATEMENT_CLASS = 'Class_';

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
        if (!($node->getType() == static::STATEMENT_CLASS)) {
            return $node;
        }

        $stmts = [];
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof ClassMethod && $node->name->toString() === $this->methodNameToRemove) {
                continue;
            }

            $stmts[] = $stmt;
        }
        $node->stmts = $stmts;

        return $node;
    }
}
