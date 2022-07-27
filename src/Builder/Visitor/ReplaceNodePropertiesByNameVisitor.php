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

class ReplaceNodePropertiesByNameVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    public const STMTS = 'stmts';

    /**
     * @var string
     */
    public const RETURN_TYPE = 'returnType';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array<string, \PhpParser\Node>
     */
    protected array $properties;

    /**
     * @param string $name
     * @param array<string, \PhpParser\Node> $properties
     */
    public function __construct(string $name, array $properties = [])
    {
        $this->name = $name;
        $this->properties = $properties;
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
        foreach ($this->properties as $key => $value) {
            if (property_exists($node, $key)) {
                $node->$key = $value;
            }
        }

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
