<?php

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node;

class PluginToPluginCollectionPositionResolver extends AbstractPluginPositionResolver
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
                $stmt->expr instanceof MethodCall === false
                || strpos(strtolower($stmt->expr->name->toString()), 'add') === false
            ) {
                continue;
            }

            /** @var \PhpParser\Node\Arg $arg */
            foreach ($stmt->expr->args as $arg) {
                if ($arg->value instanceof New_) {
                    $plugins[] = $arg->value->class->toString();
                }
            }
        }

        var_dump($plugins);

        return $plugins;
    }
}
