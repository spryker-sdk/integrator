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
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class AddPluginToChainedPluginCollectionVisitor extends NodeVisitorAbstract
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
     * @var \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface
     */
    protected PluginPositionResolverInterface $pluginPositionResolver;

    /**
     * @var bool
     */
    protected $pluginInserted = false;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface $argumentBuilder
     * @param \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface $pluginPositionResolver
     */
    public function __construct(
        ClassMetadataTransfer $classMetadataTransfer,
        ArgumentBuilderInterface $argumentBuilder,
        PluginPositionResolverInterface $pluginPositionResolver
    ) {
        $this->classMetadataTransfer = $classMetadataTransfer;
        $this->argumentBuilder = $argumentBuilder;
        $this->pluginPositionResolver = $pluginPositionResolver;
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

            $beforePlugin = $this->pluginPositionResolver->getFirstExistPluginByPositions(
                $this->getPluginList($node),
                $this->classMetadataTransfer->getBefore()->getArrayCopy(),
            );
            $afterPlugin = $this->pluginPositionResolver->getFirstExistPluginByPositions(
                $this->getPluginList($node),
                $this->classMetadataTransfer->getAfter()->getArrayCopy(),
            );
            foreach ($node->stmts as $stmt) {
                if ($this->pluginInserted) {
                    break;
                }

                if (!$this->isStatementAddPluginMethodCall($stmt)) {
                    continue;
                }

                $stmt->expr = $this->processMethodCall($stmt->expr, $beforePlugin, $afterPlugin);
            }

            foreach ($node->stmts as $stmt) {
                if ($this->pluginInserted) {
                    break;
                }

                if (!$this->isStatementAddPluginMethodCall($stmt)) {
                    continue;
                }

                $arguments = $this->argumentBuilder->createAddPluginArguments($this->classMetadataTransfer);
                $newMethodCall = (new BuilderFactory())
                    ->methodCall($stmt->expr, $stmt->expr->name, $arguments);
                $stmt->expr = $newMethodCall;
                $this->pluginInserted = true;

                break;
            }

            return $node;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall $methodCall
     * @param string|null $beforePlugin
     * @param string|null $afterPlugin
     *
     * @return \PhpParser\Node\Expr\MethodCall
     */
    protected function processMethodCall(
        MethodCall $methodCall,
        ?string $beforePlugin = null,
        ?string $afterPlugin = null
    ): MethodCall {
        /** @var \PhpParser\Node\Arg $arg */
        foreach ($methodCall->args as $arg) {
            if ($arg->value instanceof New_ && $arg->value->class->toString() === $beforePlugin) {
                $arguments = $this->argumentBuilder->createAddPluginArguments($this->classMetadataTransfer);
                $newMethodCall = (new BuilderFactory())
                    ->methodCall($methodCall->var, $methodCall->name, $arguments);
                $methodCall->var = $newMethodCall;

                $this->pluginInserted = true;

                return $methodCall;
            }

            if ($arg->value instanceof New_ && $arg->value->class->toString() === $afterPlugin) {
                $arguments = $this->argumentBuilder->createAddPluginArguments($this->classMetadataTransfer);
                $newMethodCall = (new BuilderFactory())
                    ->methodCall($methodCall, $methodCall->name, $arguments);

                $this->pluginInserted = true;

                return $newMethodCall;
            }
        }

        if ($methodCall->var instanceof MethodCall) {
            $methodCall->var = $this->processMethodCall($methodCall->var, $beforePlugin, $afterPlugin);
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

    /**
     * @param \PhpParser\Node $node
     *
     * @return array<string>
     */
    protected function getPluginList(Node $node): array
    {
        $plugins = [];

        foreach ($node->stmts as $stmt) {
            if (!$this->isStatementAddPluginMethodCall($stmt)) {
                continue;
            }

            foreach ($stmt->expr->args as $arg) {
                if ($arg->value instanceof New_) {
                    $plugins[] = $arg->value->class->toString();
                }
            }
        }

        return $plugins;
    }
}
