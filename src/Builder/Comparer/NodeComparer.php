<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Comparer;

use PhpParser\Node;

class NodeComparer implements NodeComparerInterface
{
    /**
     * @var array<\SprykerSdk\Integrator\Builder\Comparer\CompareStrategyInterface>
     */
    protected array $compareStrategies;

    /**
     * @param array<\SprykerSdk\Integrator\Builder\Comparer\CompareStrategyInterface> $compareStrategies
     */
    public function __construct(array $compareStrategies)
    {
        $this->compareStrategies = $compareStrategies;
    }

    /**
     * @param \PhpParser\Node $node
     * @param \PhpParser\Node $nodeToCompare
     * @param array<\PhpParser\Node> $classTokenTree
     *
     * @throws \SprykerSdk\Integrator\Builder\Comparer\UnsupportedComparerNodeTypeException
     *
     * @return bool
     */
    public function isEqual(Node $node, Node $nodeToCompare, array $classTokenTree = []): bool
    {
        foreach ($this->compareStrategies as $compareStrategy) {
            if ($compareStrategy->isApplicable($node, $nodeToCompare)) {
                return $compareStrategy->isEqual($node, $nodeToCompare, $classTokenTree);
            }
        }

        throw new UnsupportedComparerNodeTypeException(
            sprintf('Unsupported node types `%s` and `%s` for comparing', get_class($node), get_class($nodeToCompare)),
        );
    }
}
