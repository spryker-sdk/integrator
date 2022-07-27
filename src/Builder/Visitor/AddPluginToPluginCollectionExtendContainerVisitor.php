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
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class AddPluginToPluginCollectionExtendContainerVisitor extends NodeVisitorAbstract
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
     * @var \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface
     */
    protected $argumentBuilder;

    /**
     * @param string $methodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface $argumentBuilder
     */
    public function __construct(string $methodName, ClassMetadataTransfer $classMetadataTransfer, ArgumentBuilderInterface $argumentBuilder)
    {
        $this->methodName = $methodName;
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
        if ($node->getType() === static::STATEMENT_CLASS_METHOD && $node->name->toString() === $this->methodName) {
            $addPluginCalls = (new NodeFinder())->find($node->stmts, function (Node $node) {
                return $node instanceof MethodCall
                    && strpos(strtolower($node->name->toString()), 'add') !== false;
            });

            if (!$addPluginCalls) {
                return $node;
            }

            foreach ($node->stmts as $stmt) {
                if (
                    $stmt instanceof Expression
                    && $stmt->expr instanceof MethodCall
                    && count($stmt->expr->args) >= 2
                    && $stmt->expr->args[1]->value instanceof Closure
                ) {
                    $stmt->expr->args[1]->value = $this->handleContainerExtendClosure($stmt->expr->args[1]->value, $addPluginCalls);

                    break;
                }
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\Closure $closure
     * @param array $addPluginCalls
     *
     * @return \PhpParser\Node\Expr\Closure
     */
    protected function handleContainerExtendClosure(Closure $closure, array $addPluginCalls): Closure
    {
        $addPluginCallCount = 0;
        $newPluginAddCallIndex = 0;

        foreach ($closure->stmts as $index => $stmt) {
            if ($stmt instanceof Expression === false) {
                continue;
            }
            if ($stmt->expr instanceof MethodCall === false
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
            $arguments = $this->createAddPluginArguments();
            $newMethodCall = (new BuilderFactory())
                ->methodCall($addPluginCalls[0]->var, $addPluginCalls[0]->name, $arguments);

            array_splice($closure->stmts, $newPluginAddCallIndex, 0, [new Expression($newMethodCall)]);
        }

        return $closure;
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
