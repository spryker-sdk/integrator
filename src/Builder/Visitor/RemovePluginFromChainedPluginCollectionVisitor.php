<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class RemovePluginFromChainedPluginCollectionVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const STATEMENT_CLASS_METHOD = 'Stmt_ClassMethod';

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected $classMetadataTransfer;

    /**
     * @var bool
     */
    protected $pluginRemoved = false;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     */
    public function __construct(ClassMetadataTransfer $classMetadataTransfer)
    {
        $this->classMetadataTransfer = $classMetadataTransfer;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function enterNode(Node $node): Node
    {
        if (
            $node->getType() === static::STATEMENT_CLASS_METHOD
            && $node->name->toString() === $this->classMetadataTransfer->getTargetMethodNameOrFail()
        ) {
            $addPluginCalls = (new NodeFinder())->find($node->stmts, function (Node $node) {
                return $node instanceof MethodCall
                    && strpos(strtolower($node->name->toString()), 'add') !== false;
            });

            if (!$addPluginCalls) {
                return $node;
            }

            foreach ($node->stmts as $stmt) {
                if ($this->pluginRemoved) {
                    break;
                }

                if (!$this->isStatementAddPluginMethodCall($stmt)) {
                    continue;
                }

                $stmt->expr = $this->processMethodCall($stmt->expr);
            }

            if (!$this->pluginRemoved) {
                foreach ($node->stmts as $stmt) {
                    if (!$this->isStatementAddPluginMethodCall($stmt)) {
                        continue;
                    }

                    if ($stmt->expr instanceof MethodCall) {
                        foreach ($stmt->expr->args as $arg) {
                            if ($arg->value instanceof New_ && $arg->value->class->toString() === $this->classMetadataTransfer->getSourceOrFail()) {
                                $stmt->expr = $stmt->expr->var;

                                $this->pluginRemoved = true;

                                break 2;
                            }
                        }
                    }
                }
            }

            return $node;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall $methodCall
     *
     * @return \PhpParser\Node\Expr\MethodCall
     */
    protected function processMethodCall(MethodCall $methodCall): MethodCall
    {
        if ($methodCall->var instanceof MethodCall) {
            /** @var \PhpParser\Node\Arg $arg */
            foreach ($methodCall->var->args as $arg) {
                if ($arg->value instanceof New_ && $arg->value->class->toString() === $this->classMetadataTransfer->getSourceOrFail()) {
                    $methodCall->var = $methodCall->var->var;
                    $this->pluginRemoved = true;

                    return $methodCall;
                }
            }
        }

        if ($methodCall->var instanceof MethodCall) {
            $methodCall->var = $this->processMethodCall($methodCall->var);
        }

        return $methodCall;
    }

    /**
     * @param \PhpParser\Node\Stmt $stmt
     *
     * @return bool
     */
    protected function isStatementAddPluginMethodCall(Stmt $stmt): bool
    {
        if ($stmt instanceof Expression === false) {
            return false;
        }

        if ($stmt instanceof Expression && $stmt->expr instanceof MethodCall === false) {
            return false;
        }

        if (
            $stmt instanceof Expression
            && $stmt->expr instanceof MethodCall
            && strpos(strtolower($stmt->expr->name->toString()), 'add') === false
        ) {
            return false;
        }

        return true;
    }
}
