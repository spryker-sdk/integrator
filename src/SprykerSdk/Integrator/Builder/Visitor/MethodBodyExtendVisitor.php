<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitorAbstract;

class MethodBodyExtendVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var array
     */
    protected $methodBody;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @param string $methodName
     * @param array $methodBody
     */
    public function __construct(string $methodName, array $methodBody)
    {
        $this->methodName = $methodName;
        $this->methodBody = $methodBody;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return int|\PhpParser\Node|null
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof ClassMethod && $node->name->name === $this->methodName) {
            $this->methodFound = true;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return int|\PhpParser\Node|\PhpParser\Node[]|null
     */
    public function leaveNode(Node $node)
    {
        if (!$this->methodFound || !($node instanceof Return_)) {
            return $node;
        }

        $this->methodFound = false;

        return array_merge($this->methodBody, [$node]);
    }
}
