<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Comparer;

use PhpParser\Node;

interface CompareStrategyInterface
{
    /**
     * @param \PhpParser\Node $node
     * @param \PhpParser\Node $nodeToCompare
     * @param array<\PhpParser\Node> $classTokenTree
     *
     * @return bool
     */
    public function isEqual(Node $node, Node $nodeToCompare, array $classTokenTree = []): bool;

    /**
     * @param \PhpParser\Node $node
     * @param \PhpParser\Node $nodeToCompare
     *
     * @return bool
     */
    public function isApplicable(Node $node, Node $nodeToCompare): bool;
}
