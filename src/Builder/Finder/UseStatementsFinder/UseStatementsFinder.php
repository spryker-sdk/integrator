<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Finder\UseStatementsFinder;

use ArrayObject;
use PhpParser\NodeTraverser;

class UseStatementsFinder implements UseStatementsFinderInterface
{
    /**
     * @param array<\PhpParser\Node> $classTokenTree
     *
     * @return array<\PhpParser\Node\Stmt\Use_>
     */
    public function findUseStatements(array $classTokenTree): array
    {
        $useStatements = new ArrayObject();

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new UseStatementVisitor($useStatements));
        $traverser->traverse($classTokenTree);

        return iterator_to_array($useStatements);
    }
}
