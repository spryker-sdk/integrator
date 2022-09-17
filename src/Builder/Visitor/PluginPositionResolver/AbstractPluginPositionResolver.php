<?php

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;


abstract class AbstractPluginPositionResolver implements PluginPositionResolverInterface
{
    /**
     * @param \PhpParser\Node $node
     * @param array $positions
     * @return string|null
     */
    public function getFirstExistPluginByPositions(Node $node, array $positions): ?string
    {
        $existPlugins = [];

        foreach ($this->getPluginList($node) as $plugin) {
            $position = array_search($plugin, $positions, true);
            if($position !== false) {
                $existPlugins[$position] = $plugin;
            }
        }
        return array_shift($existPlugins) ?? array_shift($positions);
    }

    /**
     * @param \PhpParser\Node $node
     * @return array
     */
    abstract protected function getPluginList(Node $node): array;

}
