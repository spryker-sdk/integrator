<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor\AddStatementToStatementListStrategy;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;

class ArrayMergeArrayModifyAddStatementToStatementListStrategy extends AbstractAddStatementToStatementListStrategy implements AddStatementToStatementListStrategyInterface
{
    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var mixed
     */
    protected $additionalStatements;

    /**
     * @param string $methodName
     * @param mixed $additionalStatements
     */
    public function __construct(string $methodName, $additionalStatements)
    {
        $this->methodName = $methodName;
        $this->additionalStatements = $additionalStatements;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return bool
     */
    public function isApplicable(Node $node): bool
    {
        if (!$this->isArrayMergeNode($node)) {
            return false;
        }

        foreach ($node->stmts[0]->expr->args as $argument) {
            if (property_exists($argument, 'value') && $argument->value instanceof Array_) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function apply(Node $node): Node
    {
        foreach ($node->stmts[0]->expr->args as $argument) {
            if (property_exists($argument, 'value') && $argument->value instanceof Array_) {
                $argument->value->items = array_merge($argument->value->items, $this->additionalStatements);
            }
        }

        return $node;
    }
}
