<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor\AddStatementToStatementListStrategy;

use PhpParser\Node;
use PhpParser\Node\Arg;

class ArrayMergeAddStatementToStatementListStrategy extends AbstractAddStatementToStatementListStrategy implements AddStatementToStatementListStrategyInterface
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
        return $this->isArrayMergeNode($node);
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function apply(Node $node): Node
    {
        $newArguments = [];
        foreach ($this->additionalStatements as $additionalStatement) {
            $newArguments[] = new Arg($additionalStatement->value);
        }
        $node->stmts[0]->expr->args = array_merge($node->stmts[0]->expr->args, $newArguments);

        return $node;
    }
}
