<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor\AddStatementToStatementListStrategy;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Return_;

class AbstractAddStatementToStatementListStrategy
{
    /**
     * @var string
     */
    protected const ARRAY_MERGE_FUNCTION = 'array_merge';

    /**
     * @param \PhpParser\Node $node
     *
     * @return bool
     */
    public function isArrayMergeNode(Node $node): bool
    {
        $expression = $node->stmts[0]->expr;
        if (!property_exists($expression, 'name')) {
            return false;
        }

        return $node->stmts[0] instanceof Return_
            && $node->stmts[0]->expr instanceof FuncCall
            && $expression->name->parts[0] === static::ARRAY_MERGE_FUNCTION;
    }
}
