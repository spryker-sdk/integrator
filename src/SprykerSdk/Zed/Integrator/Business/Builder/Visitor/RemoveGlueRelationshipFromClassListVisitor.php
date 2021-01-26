<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class RemoveGlueRelationshipFromClassListVisitor extends NodeVisitorAbstract
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
     * @var string
     */
    protected $keyToRemove;

    /**
     * @var string
     */
    protected $keyClassToRemove;

    /**
     * @param string $targetMethodName
     * @param string $keyClassToRemove
     * @param string $keyConstToRemove
     * @param string $classNameToRemove
     */
    public function __construct(string $targetMethodName, string $keyClassToRemove, string $keyConstToRemove, string $classNameToRemove)
    {
        $this->targetMethodName = ltrim($targetMethodName, '\\');
        $this->classNameToRemove = ltrim($classNameToRemove, '\\');
        $this->keyClassToRemove = ltrim($keyClassToRemove, '\\');
        $this->keyToRemove = $keyConstToRemove;
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

        if ($node->getType() === 'Stmt_ClassMethod' && $node->name->toString() !== $this->targetMethodName) {
            $this->methodFound = false;

            return $node;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return int|\PhpParser\Node|\PhpParser\Node\Stmt\Expression|null
     */
    public function leaveNode(Node $node)
    {
        if (!$this->methodFound) {
            return $node;
        }

        if (!($node instanceof Expression) || !($node->expr instanceof MethodCall) || $node->expr->name->toString() !== 'addRelationship') {
            return $node;
        }

        /** @var \PhpParser\Node\Expr\ClassConstFetch $firstParam */
        $firstParam = $node->expr->args[0]->value;

        /** @var \PhpParser\Node\Expr\New_ $secondParam */
        $secondParam = $node->expr->args[1]->value;

        if (!($firstParam instanceof ClassConstFetch) || $firstParam->class->toString() !== $this->keyClassToRemove || $firstParam->name->toString() !== $this->keyToRemove) {
            return $node;
        }

        if (!($secondParam instanceof New_) || $secondParam->class->toString() !== $this->classNameToRemove) {
            return $node;
        }

        return NodeTraverser::REMOVE_NODE;
    }
}
