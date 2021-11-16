<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;

class AddMethodVisitor extends NodeVisitorAbstract
{
    /**
     * @var \PhpParser\Node
     */
    protected $classMethodNode;

    /**
     * @param \PhpParser\Node $classMethodNode
     */
    public function __construct(Node $classMethodNode)
    {
        $this->classMethodNode = $classMethodNode;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|array|int|null
     */
    public function enterNode(Node $node)
    {
        if (!($node instanceof Class_)) {
            return $node;
        }

        $stmts = $node->stmts;
        $stmts[] = $this->classMethodNode;
        $node->stmts = $stmts;

        return $node;
    }
}
