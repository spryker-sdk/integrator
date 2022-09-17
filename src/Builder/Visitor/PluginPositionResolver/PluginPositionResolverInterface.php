<?php

namespace SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;

interface PluginPositionResolverInterface
{
    /**
     * @param \PhpParser\Node $node
     * @param array $positions
     * @return string|null
     */
    public function getFirstExistPluginByPositions(Node $node, array $positions): ?string;
}
