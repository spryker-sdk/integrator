<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class RemovePluginFromPluginCollectionExtendContainerVisitor extends NodeVisitorAbstract
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
     * @var \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected $classMetadataTransfer;

    /**
     * @param string $methodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     */
    public function __construct(string $methodName, ClassMetadataTransfer $classMetadataTransfer)
    {
        $this->methodName = $methodName;
        $this->classMetadataTransfer = $classMetadataTransfer;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function enterNode(Node $node): Node
    {
        if ($node->getType() === static::STATEMENT_CLASS_METHOD && $node->name->toString() === $this->methodName) {
            foreach ($node->stmts as $stmt) {
                if (
                    $stmt instanceof Expression
                    && $stmt->expr instanceof MethodCall
                    && count($stmt->expr->args) >= 2
                    && $stmt->expr->args[1]->value instanceof Closure
                ) {
                    $stmt->expr->args[1]->value = $this->handleContainerExtendClosure($stmt->expr->args[1]->value);

                    break;
                }
            }

            return $node;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\Closure $closure
     *
     * @return \PhpParser\Node\Expr\Closure
     */
    protected function handleContainerExtendClosure(Closure $closure): Closure
    {
        $pluginToRemoveIndex = 0;

        foreach ($closure->stmts as $index => $stmt) {
            if ($stmt instanceof Expression === false) {
                continue;
            }

            if (
                $stmt->expr instanceof MethodCall === false
                || strpos(strtolower($stmt->expr->name->toString()), 'add') === false
            ) {
                continue;
            }

            /** @var \PhpParser\Node\Arg $arg */
            foreach ($stmt->expr->args as $arg) {
                if ($arg->value instanceof New_ && $arg->value->class->toString() === $this->classMetadataTransfer->getSource()) {
                    $pluginToRemoveIndex = $index;

                    break 2;
                }
            }
        }

        if ($pluginToRemoveIndex !== null) {
            array_splice($closure->stmts, $pluginToRemoveIndex, 1);
        }

        return $closure;
    }
}
