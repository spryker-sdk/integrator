<?php

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;

class PluginToPluginListPositionResolver extends AbstractPluginPositionResolver
{
    /**
     * @param \PhpParser\Node $node
     *
     * @return array
     */
    protected function getPluginList(Node $node): array
    {
        $plugins = [];

        foreach ($node->items as $item) {
            if ($item === null || !($item->value instanceof New_)) {
                continue;
            }

            $plugins[] = $item->value->class->toString();
        }

        return $plugins;
    }
}
