<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class AddPluginToPluginCollectionVisitor extends NodeVisitorAbstract
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
     * @var \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface
     */
    protected $argumentBuilder;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface $argumentBuilder
     */
    public function __construct(ClassMetadataTransfer $classMetadataTransfer, ArgumentBuilderInterface $argumentBuilder)
    {
        $this->classMetadataTransfer = $classMetadataTransfer;
        $this->argumentBuilder = $argumentBuilder;
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
            /** @var array<\PhpParser\Node\Expr\MethodCall> $addPluginCalls */
            $addPluginCalls = (new NodeFinder())->find($node->stmts, function (Node $node) {
                return $node instanceof MethodCall
                    && strpos(strtolower($node->name->toString()), 'add') !== false;
            });

            if (!$addPluginCalls) {
                return $node;
            }

            $addPluginCallCount = 0;
            $newPluginAddCallIndex = 0;

            foreach ($node->stmts as $index => $stmt) {
                if (
                    $stmt->expr instanceof MethodCall === false
                    || strpos(strtolower($stmt->expr->name->toString()), 'add') === false
                ) {
                    continue;
                }
                $addPluginCallCount++;

                /** @var \PhpParser\Node\Arg $arg */
                foreach ($stmt->expr->args as $arg) {
                    if ($arg->value instanceof New_ && $arg->value->class->toString() === $this->classMetadataTransfer->getBefore()) {
                        $newPluginAddCallIndex = $index;

                        break 2;
                    }

                    if ($arg->value instanceof New_ && $arg->value->class->toString() === $this->classMetadataTransfer->getAfter()) {
                        $newPluginAddCallIndex = $index + 1;

                        break 2;
                    }
                }

                if ($addPluginCallCount === count($addPluginCalls)) {
                    $newPluginAddCallIndex = $index + 1;

                    break;
                }
            }

            if ($addPluginCallCount) {
                $arguments = $this->argumentBuilder->createAddPluginArguments($this->classMetadataTransfer);
                $newMethodCall = (new BuilderFactory())
                    ->methodCall($addPluginCalls[0]->var, $addPluginCalls[0]->name, $arguments);

                array_splice($node->stmts, $newPluginAddCallIndex, 0, [new Expression($newMethodCall)]);
            }

            return $node;
        }

        return $node;
    }
}
