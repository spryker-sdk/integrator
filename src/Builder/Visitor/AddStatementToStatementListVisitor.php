<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Builder\Visitor\AddStatementToStatementListStrategy\ArrayMergeAddStatementToStatementListStrategy;
use SprykerSdk\Integrator\Builder\Visitor\AddStatementToStatementListStrategy\ArrayMergeArrayModifyAddStatementToStatementListStrategy;

class AddStatementToStatementListVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const STATEMENT_CLASS_METHOD = 'Stmt_ClassMethod';

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var mixed
     */
    protected $additionalStatements;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @var array<int, object>
     */
    protected $strategiesCache = [];

    /**
     * @var array<string>
     */
    protected const STRATEGIES = [
        ArrayMergeArrayModifyAddStatementToStatementListStrategy::class,
        ArrayMergeAddStatementToStatementListStrategy::class,
    ];

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
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if ($node->getType() === static::STATEMENT_CLASS_METHOD && $node->name->toString() === $this->methodName) {
            $this->addStatements($node);

            return $node;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    protected function addStatements(Node $node): Node
    {
        foreach ($this->getStrategies() as $strategy) {
            /** @var \SprykerSdk\Integrator\Builder\Visitor\AddStatementToStatementListStrategy\AddStatementToStatementListStrategyInterface $strategy */
            if ($strategy->isApplicable($node)) {
                return $strategy->apply($node);
            }
        }

        $node->stmts[0]->expr->items = array_merge($node->stmts[0]->expr->items, $this->additionalStatements);

        return $node;
    }

    /**
     * @return array<int, object>
     */
    protected function getStrategies(): array
    {
        if ($this->strategiesCache) {
            return $this->strategiesCache;
        }

        foreach (static::STRATEGIES as $strategy) {
            $this->strategiesCache[] = new $strategy($this->methodName, $this->additionalStatements);
        }

        return $this->strategiesCache;
    }
}
