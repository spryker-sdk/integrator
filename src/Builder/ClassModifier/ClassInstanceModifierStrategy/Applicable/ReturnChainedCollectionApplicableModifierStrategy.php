<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;

class ReturnChainedCollectionApplicableModifierStrategy implements ApplicableModifierStrategyInterface
{
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isApplicable(ClassMethod $node): bool
    {
        if (
            $node->getReturnType() instanceof Identifier
            && strpos(strtolower($node->getReturnType()->toString()), 'collection') === false
        ) {
            return false;
        }

        if (!$node->stmts) {
            return false;
        }

        $lastNode = end($node->stmts);

        if (
            $lastNode instanceof Return_
            && $lastNode->expr instanceof Variable
            && is_string($lastNode->expr->name)
            && strpos(strtolower($lastNode->expr->name), 'collection') === false
        ) {
            return false;
        }

        return (bool)(new NodeFinder())->findFirst($node->stmts, function (Node $node) {
            return $node instanceof Expression
                && $node->expr instanceof MethodCall
                && strpos(strtolower($node->expr->name->toString()), 'add') !== false
                && $node->expr->var instanceof MethodCall === true;
        });
    }
}
