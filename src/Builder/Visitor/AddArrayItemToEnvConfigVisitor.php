<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;

class AddArrayItemToEnvConfigVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected $target;

    /**
     * @var array<\PhpParser\Node>
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isConfigAssingFound = false;

    /**
     * @var bool
     */
    protected $isTargetFound = false;

    /**
     * @var bool
     */
    protected $isValueApplied = false;

    /**
     * @param string $target
     * @param \PhpParser\Node\Expr\ArrayItem $value
     */
    public function __construct(string $target, ArrayItem $value)
    {
        $this->target = $target;
        $this->value = $value;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if ($this->isValueApplied) {
            return $node;
        }

        if ($node instanceof Expression) {
            $this->isConfigAssingFound = false;
            if ($this->isConfigAssignStatement($node)) {
                $this->isConfigAssingFound = true;
            }
        }

        if ($this->isConfigAssingFound && $node instanceof Assign) {
            /** @var \PhpParser\Node\Expr\ArrayDimFetch $arrayDimFetchExpression */
            $arrayDimFetchExpression = $node->var;
            /** @var \PhpParser\Node\Expr\ClassConstFetch $constant */
            $constant = $arrayDimFetchExpression->dim;

            $key = sprintf('\%s::%s', $constant->class->toString(), $constant->name->toString());

            if ($key === $this->target) {
                $this->isTargetFound = true;
            }
        }

        if ($this->isTargetFound) {
            if ($node instanceof Array_) {
                $node->items[] = $this->value;
                $this->isValueApplied = true;
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $statement
     *
     * @return bool
     */
    protected function isConfigAssignStatement(Node $statement): bool
    {
        return $statement instanceof Expression
            && $statement->expr instanceof Assign
            && $statement->expr->var instanceof ArrayDimFetch
            && $statement->expr->var->dim instanceof ClassConstFetch
            && $statement->expr->var->var instanceof Variable
            && $statement->expr->var->var->name === 'config';
    }
}
