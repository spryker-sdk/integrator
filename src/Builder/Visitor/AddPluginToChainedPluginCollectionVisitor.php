<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Helper\ClassHelper;
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
     * @var bool
     */
    protected $pluginInserted = false;

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

            foreach ($node->stmts as $stmt) {
                if ($this->pluginInserted) {
                    break;
                }

                if (!$this->isStatementAddPluginMethodCall($stmt)) {
                    continue;
                }

                $stmt->expr = $this->processMethodCall($stmt->expr);
            }

            foreach ($node->stmts as $stmt) {
                if ($this->pluginInserted) {
                    break;
                }

                if (!$this->isStatementAddPluginMethodCall($stmt)) {
                    continue;
                }

                $arguments = $this->createAddPluginArguments();
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
     *
     * @return \PhpParser\Node\Expr\MethodCall
     */
    protected function processMethodCall(MethodCall $methodCall): MethodCall
    {
        /** @var \PhpParser\Node\Arg $arg */
        foreach ($methodCall->args as $arg) {
            if ($arg->value instanceof New_ && $arg->value->class->toString() === $this->classMetadataTransfer->getBefore()) {
                $arguments = $this->createAddPluginArguments();
                $newMethodCall = (new BuilderFactory())
                    ->methodCall($methodCall->var, $methodCall->name, $arguments);
                $methodCall->var = $newMethodCall;

                $this->pluginInserted = true;

                return $methodCall;
            }

            if ($arg->value instanceof New_ && $arg->value->class->toString() === $this->classMetadataTransfer->getAfter()) {
                $arguments = $this->createAddPluginArguments();
                $newMethodCall = (new BuilderFactory())
                    ->methodCall($methodCall, $methodCall->name, $arguments);

                $this->pluginInserted = true;

                return $newMethodCall;
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

    /**
     * @return array<\PhpParser\Node\Arg>
     */
    protected function createAddPluginArguments(): array
    {
        $args = [];
        $builderFactory = new BuilderFactory();

        $constructorArgumentValues = [];
        if ($this->classMetadataTransfer->getConstructorArguments()->count()) {
            $constructorArgumentValues = $this->argumentBuilder->getArguments(
                $this->classMetadataTransfer->getConstructorArguments()->getArrayCopy(),
            );
        }

        if ($this->classMetadataTransfer->getPrependArguments()->count()) {
            $prependArgumentValues = $this->argumentBuilder->getArguments(
                $this->classMetadataTransfer->getPrependArguments()->getArrayCopy(),
            );

            $args = array_merge($args, $prependArgumentValues);
        }

        $mainArgument = new Arg(
            $builderFactory->new(
                (new ClassHelper())->getShortClassName($this->classMetadataTransfer->getSourceOrFail()),
                $builderFactory->args($constructorArgumentValues),
            ),
        );
        $args = array_merge($args, $builderFactory->args([$mainArgument]));

        if ($this->classMetadataTransfer->getAppendArguments()->count()) {
            $appendArgumentValues = $this->argumentBuilder->getArguments(
                $this->classMetadataTransfer->getAppendArguments()->getArrayCopy(),
            );

            $args = array_merge($args, $appendArgumentValues);
        }

        return $args;
    }
}
