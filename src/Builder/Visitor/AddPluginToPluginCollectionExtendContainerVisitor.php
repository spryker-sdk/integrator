<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolver;
use SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class AddPluginToPluginCollectionExtendContainerVisitor extends NodeVisitorAbstract
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
            $addPluginCalls = (new NodeFinder())->find($node->stmts, function (Node $node) {
                return $node instanceof MethodCall
                    && strpos(strtolower($node->name->toString()), 'add') !== false;
            });

            if (!$addPluginCalls) {
                return $node;
            }

            $pluginPositionResolver = $this->getPluginPositionResolver();
            $beforePlugin = $pluginPositionResolver->getFirstExistPluginByPositions(
                $this->getPluginList($node),
                $this->classMetadataTransfer->getBefore()->getArrayCopy(),
            );
            $afterPlugin = $pluginPositionResolver->getFirstExistPluginByPositions(
                $this->getPluginList($node),
                $this->classMetadataTransfer->getAfter()->getArrayCopy(),
            );
            foreach ($node->stmts as $stmt) {
                if (
                    $stmt instanceof Expression
                    && $stmt->expr instanceof MethodCall
                    && count($stmt->expr->args) >= 2
                    && $stmt->expr->args[1]->value instanceof Closure
                ) {
                    $stmt->expr->args[1]->value = $this->handleContainerExtendClosure(
                        $stmt->expr->args[1]->value,
                        $addPluginCalls,
                        $beforePlugin,
                        $afterPlugin,
                    );

                    break;
                }
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\Closure $closure
     * @param array $addPluginCalls
     * @param string|null $beforePlugin
     * @param string|null $afterPlugin
     *
     * @return \PhpParser\Node\Expr\Closure
     */
    protected function handleContainerExtendClosure(
        Closure $closure,
        array $addPluginCalls,
        ?string $beforePlugin = null,
        ?string $afterPlugin = null
    ): Closure {
        $addPluginCallCount = 0;
        $newPluginAddCallIndex = 0;
        $firstAddPluginLine = null;

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
            if ($firstAddPluginLine === null) {
                $firstAddPluginLine = $index;
            }

            $addPluginCallCount++;

            /** @var \PhpParser\Node\Arg $arg */
            foreach ($stmt->expr->args as $arg) {
                if ($arg->value instanceof New_ && $arg->value->class->toString() === $beforePlugin) {
                    $newPluginAddCallIndex = $index;

                    break 2;
                }

                if ($arg->value instanceof New_ && $arg->value->class->toString() === $afterPlugin) {
                    $newPluginAddCallIndex = $index + 1;

                    break 2;
                }
            }

            if ($addPluginCallCount === count($addPluginCalls) && $beforePlugin) {
                $newPluginAddCallIndex = $firstAddPluginLine;

                break;
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

            array_splice($closure->stmts, (int)$newPluginAddCallIndex, 0, [new Expression($newMethodCall)]);
        }

        return $closure;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return array<string>
     */
    protected function getPluginList(Node $node): array
    {
        $plugins = [];

        foreach ($node->stmts as $stmt) {
            if (
                $stmt instanceof Expression
                && $stmt->expr instanceof MethodCall
                && count($stmt->expr->args) >= 2
                && $stmt->expr->args[1]->value instanceof Closure
            ) {
                /** @var \PhpParser\Node\Expr\Closure $closure */
                $closure = $stmt->expr->args[1]->value;
                foreach ($closure->stmts as $stmt) {
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
                        if ($arg->value instanceof New_) {
                            $plugins[] = $arg->value->class->toString();
                        }
                    }
                }
            }
        }

        return $plugins;
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface
     */
    protected function getPluginPositionResolver(): PluginPositionResolverInterface
    {
        return new PluginPositionResolver();
    }
}
