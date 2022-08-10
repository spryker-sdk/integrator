<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\ApplicableCheck;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;

class CheckApplicableClassInstanceReturnExtendContainerModifierStrategy implements CheckApplicableInterface
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
            && $node->getReturnType()->toString() !== 'Container'
        ) {
            return false;
        }

        if (!$node->stmts) {
            return false;
        }

        $containerExtendCallExists = false;

        foreach ($node->stmts as $stmt) {
            if (
                $stmt instanceof Expression
                && $stmt->expr instanceof MethodCall
                && $stmt->expr->var instanceof Variable
                && $stmt->expr->var->name === 'container'
                && $stmt->expr->name instanceof Identifier
                && $stmt->expr->name->name === 'extend'
            ) {
                $containerExtendCallExists = true;
            }
        }

        $addPluginCallsExist = (bool)(new NodeFinder())->findFirst($node->stmts, function (Node $node) {
            return $node instanceof MethodCall
                && strpos(strtolower($node->name->toString()), 'add') !== false;
        });

        $addPluginChainedCallsExist = (bool)(new NodeFinder())->findFirst($node->stmts, function (Node $node) {
            return $node instanceof MethodCall
                && strpos(strtolower($node->name->toString()), 'add') !== false
                && $node->var instanceof MethodCall === true;
        });

        return $containerExtendCallExists && $addPluginCallsExist && !$addPluginChainedCallsExist;
    }
}
