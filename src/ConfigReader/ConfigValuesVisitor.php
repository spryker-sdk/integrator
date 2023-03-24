<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ConfigReader;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;

class ConfigValuesVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const CONFIG_VAR_NAME = 'config';

    /**
     * @var array<string>
     */
    protected $configKeysToFind;

    /**
     * @var array<string, mixed>
     */
    protected $foundValues = [];

    /**
     * @param array $configKeysToFind
     */
    public function __construct(array $configKeysToFind)
    {
        $this->configKeysToFind = $configKeysToFind;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function enterNode(Node $node): Node
    {
        if (
            $node instanceof Expression
            && $node->expr instanceof Assign
            && $node->expr->var instanceof ArrayDimFetch
            && $node->expr->var->var instanceof Variable
            && $node->expr->var->var->name === static::CONFIG_VAR_NAME
            && $node->expr->var->dim instanceof ClassConstFetch
            && $node->expr->var->dim->name instanceof Identifier
            && in_array($this->getFullConstName($node->expr->var->dim), $this->configKeysToFind, true)
        ) {
            $this->foundValues[$this->getFullConstName($node->expr->var->dim)] = $this->getValuesFromAssignExpression($node->expr);
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\Assign $expr
     *
     * @return array<mixed>
     */
    protected function getValuesFromAssignExpression(Assign $expr): array
    {
        if (!($expr->expr instanceof Array_)) {
            return [];
        }

        return $this->getValuesFromArray($expr->expr);
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $array
     *
     * @return array<mixed>
     */
    protected function getValuesFromArray(Array_ $array): array
    {
        $values = [];

        /** @var \PhpParser\Node\Expr\ArrayItem $item */
        foreach ($array->items as $item) {
            $itemValue = $item->value;

            if ($itemValue instanceof Scalar && property_exists($itemValue, 'value')) {
                $values[] = $itemValue->value;
            }
        }

        return $values;
    }

    /**
     * @param \PhpParser\Node\Expr\ClassConstFetch $classConstFetch
     *
     * @return string
     */
    protected function getFullConstName(ClassConstFetch $classConstFetch): string
    {
        return sprintf('%s::%s', $classConstFetch->class->toString(), $classConstFetch->name->toString());
    }

    /**
     * @return array<string, mixed>
     */
    public function getFoundValues(): array
    {
        return $this->foundValues;
    }
}
