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
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface;
use SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface;
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
     * @var \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface
     */
    protected PluginPositionResolverInterface $pluginPositionResolver;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface
     */
    protected ClassLoaderInterface $classLoader;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface $argumentBuilder
     * @param \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface $pluginPositionResolver
     * @param \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface $classLoader
     */
    public function __construct(
        ClassMetadataTransfer $classMetadataTransfer,
        ArgumentBuilderInterface $argumentBuilder,
        PluginPositionResolverInterface $pluginPositionResolver,
        ClassLoaderInterface $classLoader
    ) {
        $this->classMetadataTransfer = $classMetadataTransfer;
        $this->argumentBuilder = $argumentBuilder;
        $this->pluginPositionResolver = $pluginPositionResolver;
        $this->classLoader = $classLoader;
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
            $firstAddPluginLine = null;
            $beforePlugin = $this->pluginPositionResolver->getFirstExistPluginByPositions(
                $this->getPluginList($node),
                $this->classMetadataTransfer->getBefore()->getArrayCopy(),
            );
            $afterPlugin = $this->pluginPositionResolver->getFirstExistPluginByPositions(
                $this->getPluginList($node),
                $this->classMetadataTransfer->getAfter()->getArrayCopy(),
            );
            foreach ($node->stmts as $index => $stmt) {
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
                $lastCall = end($addPluginCalls);
                $newMethodCall = (new BuilderFactory())
                    ->methodCall($lastCall->var, $this->getMethodName($node) ?: $lastCall->name, $arguments);

                array_splice($node->stmts, $newPluginAddCallIndex, 0, [new Expression($newMethodCall)]);
            }

            return $node;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return string|null
     */
    protected function getMethodName(Node $node): ?string
    {
        if (!($node instanceof ClassMethod) || !$node->returnType || !$node->returnType->toString()) {
            return null;
        }
        $sourceClass = $this->classLoader->loadClass($this->classMetadataTransfer->getSourceOrFail());
        $returnClass = $this->classLoader->loadClass($node->returnType->toString());

        /**
         * @var array<\PhpParser\Node\Stmt\ClassMethod> $possibleNodeMethods
         */
        $possibleNodeMethods = (new NodeFinder())->find($returnClass->getClassTokenTree(), function (Node $node) {
            return $node instanceof ClassMethod && $node->flags === Class_::MODIFIER_PUBLIC;
        });

        /**
         * @var array<\PhpParser\Node\Stmt\Class_> $sourceNodeInterfaces
         */
        $sourceNodeInterfaces = (new NodeFinder())->find($sourceClass->getClassTokenTree(), function (Node $node) {
            return $node instanceof Class_ && $node->implements;
        });
        if (!$sourceNodeInterfaces) {
            return null;
        }
        $sourceInterfaces = current($sourceNodeInterfaces)->implements;
        foreach ($possibleNodeMethods as $possibleMethod) {
            $methodName = $possibleMethod->name->toString();
            $interfaces = array_map(static fn (Node\Param $param): string => $param->type ? $param->type->toString() : '', $possibleMethod->params ?: []);
            if (array_intersect($sourceInterfaces, $interfaces)) {
                return $methodName;
            }
        }

        return null;
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

        return $plugins;
    }
}
