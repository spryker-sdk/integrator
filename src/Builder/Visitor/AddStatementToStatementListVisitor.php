<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Builder\Comparer\NodeComparerInterface;
use SprykerSdk\Integrator\Builder\Comparer\UnsupportedComparerNodeTypeException;
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
     * @var \SprykerSdk\Integrator\Builder\Comparer\NodeComparerInterface
     */
    protected NodeComparerInterface $nodeComparer;

    /**
     * @var array<mixed>
     */
    protected array $classTreeNodes;

    /**
     * @param string $methodName
     * @param mixed $additionalStatements
     * @param \SprykerSdk\Integrator\Builder\Comparer\NodeComparerInterface $nodeComparer
     */
    public function __construct(string $methodName, $additionalStatements, NodeComparerInterface $nodeComparer)
    {
        $this->methodName = $methodName;
        $this->additionalStatements = $additionalStatements;
        $this->nodeComparer = $nodeComparer;
    }

    /**
     * @param array<\PhpParser\Node> $nodes
     *
     * @return array<\PhpParser\Node>|null
     */
    public function beforeTraverse(array $nodes): ?array
    {
        $this->classTreeNodes = $nodes;

        return null;
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

        $node->stmts[0]->expr->items = $this->addUniqueItems($node->stmts[0]->expr->items, $this->additionalStatements);

        return $node;
    }

    /**
     * @param array<\PhpParser\Node\Expr\ArrayItem> $items
     * @param array<\PhpParser\Node\Expr\ArrayItem> $newItems
     *
     * @return array<\PhpParser\Node\Expr\ArrayItem>
     */
    protected function addUniqueItems(array $items, array $newItems): array
    {
        $uniqueArrayItems = $items;

        foreach ($newItems as $newItem) {
            foreach ($items as $item) {
                if ($this->isArrayItemsEqual($item, $newItem)) {
                    continue 2;
                }
            }

            $uniqueArrayItems[] = $newItem;
        }

        return $uniqueArrayItems;
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem $item
     * @param \PhpParser\Node\Expr\ArrayItem $newItem
     *
     * @return bool
     */
    protected function isArrayItemsEqual(ArrayItem $item, ArrayItem $newItem): bool
    {
        try {
            if (!$this->isArrayItemKeysEqual($item, $newItem)) {
                return false;
            }

            return $this->nodeComparer->isEqual($item->value, $newItem->value, $this->classTreeNodes);
        } catch (UnsupportedComparerNodeTypeException $e) {
            return false;
        }
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem $item
     * @param \PhpParser\Node\Expr\ArrayItem $newItem
     *
     * @return bool
     */
    protected function isArrayItemKeysEqual(ArrayItem $item, ArrayItem $newItem): bool
    {
        if ($item->key === null && $newItem->key === null) {
            return true;
        }

        if ($item->key instanceof Expr && $newItem->key instanceof Expr && $this->nodeComparer->isEqual($item->key, $newItem->key, $this->classTreeNodes)) {
            return true;
        }

        return false;
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
