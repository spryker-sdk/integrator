<?php

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;

class PluginToPluginCollectionExtendContainerPositionResolver extends AbstractPluginPositionResolver
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
            if (
                $stmt instanceof Expression
                && $stmt->expr instanceof MethodCall
                && count($stmt->expr->args) >= 2
                && $stmt->expr->args[1]->value instanceof Closure
            ) {
                /** @var \PhpParser\Node\Expr\Closure $closure */
                $closure = $stmt->expr->args[1]->value;
                foreach ($closure->stmts as $stmt) {
                    if ($stmt instanceof Expression === false) {
                        continue;
                    }
                    if (
                        $stmt->expr instanceof MethodCall === false
                        || strpos(strtolower($stmt->expr->name->toString()), 'add') === false
                    ) {
                        continue;
                    }

                    /** @var \PhpParser\Node\Arg $arg */
                    foreach ($stmt->expr->args as $arg) {
                        if ($arg->value instanceof New_ ) {
                            $plugins[] = $arg->value->class->toString();
                        }
                    }

                }
            }
        }

        return $plugins;
    }
}
