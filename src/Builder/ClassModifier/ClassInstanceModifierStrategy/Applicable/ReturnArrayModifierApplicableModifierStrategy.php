<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;

class ReturnArrayModifierApplicableModifierStrategy implements ApplicableModifierStrategyInterface
{
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isApplicable(ClassMethod $node): bool
    {
        if (
            $node->getReturnType()
            && $node->getReturnType() instanceof Identifier
            && $node->getReturnType()->name === 'array'
        ) {
            return true;
        }

        if (!$node->stmts) {
            return false;
        }

        $lastNode = end($node->stmts);

        if ($lastNode instanceof Return_ && $lastNode->expr instanceof Array_) {
            return true;
        }

        if (
            $lastNode instanceof Return_
            && $lastNode->expr instanceof FuncCall
            && strpos($lastNode->expr->name->toString(), 'array_') === 0
        ) {
            return true;
        }

        if ($lastNode instanceof Return_ && $lastNode->expr instanceof Variable) {
            $varName = $lastNode->expr->name;

            return (bool)(new NodeFinder())->findFirst($node->stmts, function (Node $node) use ($varName) {
                return $node instanceof Assign
                    && $node->var instanceof Variable
                    && $node->var->name === $varName
                    && $node->expr instanceof Array_;
            });
        }

        return false;
    }
}
