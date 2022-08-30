<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;

class ReplaceNodeStmtByNameVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array<\PhpParser\Node>
     */
    protected $stmt;

    /**
     * @param string $name
     * @param array<\PhpParser\Node> $stmts
     */
    public function __construct(string $name, array $stmts)
    {
        $this->name = $name;
        $this->stmt = $stmts;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if (!$this->isNecessaryNode($node)) {
            return $node;
        }

        $node->stmts = $this->stmt;

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return bool
     */
    protected function isNecessaryNode(Node $node): bool
    {
        return $this->isExistsNodeStmtsAndNameFields($node) && $this->isNodeNameCorrect($node);
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return bool
     */
    protected function isNodeNameCorrect(Node $node): bool
    {
        return ($node->name instanceof Name && $node->name->toString() === $this->name)
            || ($node->name instanceof Identifier && $node->name->name === $this->name)
            || $node->name === $this->name;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return bool
     */
    protected function isExistsNodeStmtsAndNameFields(Node $node): bool
    {
        return property_exists($node, 'stmts') && property_exists($node, 'name');
    }
}