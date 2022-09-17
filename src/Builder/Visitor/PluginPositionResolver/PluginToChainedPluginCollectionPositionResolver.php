<?php

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;

class PluginToChainedPluginCollectionPositionResolver extends AbstractPluginPositionResolver
{
    /**
     * @param \PhpParser\Node $node
     *
     * @return array
     */
    protected function getPluginList(Node $node): array
    {
        $plugins = [];

        foreach ($node->stmts as $stmt) {
            if (!$this->isStatementAddPluginMethodCall($stmt)) {
                continue;
            }


            foreach ($stmt->expr->args as $arg) {
                if ($arg->value instanceof New_) {
                    $plugins[] = $arg->value->class->toString();
                }
            }
        }

        return $plugins;
    }

    /**
     * @param \PhpParser\Node\Stmt $stmt
     *
     * @return bool
     */
    protected function isStatementAddPluginMethodCall(Stmt $stmt): bool
    {
        if ($stmt instanceof Expression === false) {
            return false;
        }

        if ($stmt instanceof Expression && $stmt->expr instanceof MethodCall === false) {
            return false;
        }

        if (
            $stmt instanceof Expression
            && $stmt->expr instanceof MethodCall
            && strpos(strtolower($stmt->expr->name->toString()), 'add') === false
        ) {
            return false;
        }

        return true;
    }
}
