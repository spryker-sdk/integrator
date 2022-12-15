<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class AddMethodCallToCallListVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const ARRAY_MERGE_FUNCTION = 'array_merge';

    /**
     * @var string
     */
    protected const THIS_KEY = 'this';

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected $classMetadataTransfer;

    /**
     * @var bool
     */
    protected $methodFound = false;

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
     * @return \PhpParser\Node|int
     */
    public function enterNode(Node $node)
    {
        $callMetadataTransfer = $this->classMetadataTransfer->getCall();
        if (!$callMetadataTransfer) {
            return $node;
        }

        $callTargetMethodName = $this->getMethodNameFromNamespaceName($callMetadataTransfer->getTargetOrFail());
        if ($node instanceof ClassMethod && $node->name->toString() === $callTargetMethodName) {
            $this->methodFound = true;

            return $node;
        }

        if (!$this->methodFound) {
            return $node;
        }

        if ($node instanceof FuncCall && $this->isArrayMergeFuncCallNode($node)) {
            $this->addNewCallIntoArrayMergeFuncNode($node);

            return $this->successfullyProcessed();
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return bool
     */
    protected function isArrayMergeFuncCallNode(FuncCall $node): bool
    {
        return $node->name instanceof Name && $node->name->parts[0] === static::ARRAY_MERGE_FUNCTION;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewCallIntoArrayMergeFuncNode(FuncCall $node): Node
    {
        $callMetadataTransfer = $this->classMetadataTransfer->getCall();
        if (!$callMetadataTransfer) {
            return $node;
        }

        $calledMethods = $this->getCalledMethods($node);
        $before = $callMetadataTransfer->getBefore();
        if ($before) {
            $beforePosition = $this->getPositionByNamespace($calledMethods, $before);
            if ($beforePosition !== null) {
                return $this->addNewCallIntoArrayMergeFuncNodeByPosition($node, $beforePosition);
            }
        }

        $after = $callMetadataTransfer->getAfter();
        if ($after) {
            $afterPosition = $this->getPositionByNamespace($calledMethods, $after);
            if ($afterPosition !== null) {
                return $this->addNewCallIntoArrayMergeFuncNodeByPosition($node, $afterPosition + 1);
            }
        }

        $node->args[] = $this->createFunctionArgWithMethodCall();

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     * @param int $position
     *
     * @return \PhpParser\Node
     */
    protected function addNewCallIntoArrayMergeFuncNodeByPosition(FuncCall $node, int $position): Node
    {
        array_splice($node->args, $position, 0, [$this->createFunctionArgWithMethodCall()]);

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return array<string>
     */
    protected function getCalledMethods(FuncCall $node): array
    {
        $result = [];

        $nodeFinder = new NodeFinder();
        /** @var array<\PhpParser\Node\Expr\MethodCall> $calledMethods */
        $calledMethods = $nodeFinder->findInstanceOf($node, MethodCall::class);
        foreach ($calledMethods as $method) {
            /** @var \PhpParser\Node\Identifier $methodName */
            $methodName = $method->name;
            $result[] = $methodName->name;
        }

        return $result;
    }

    /**
     * @return \PhpParser\Node\Arg
     */
    protected function createFunctionArgWithMethodCall(): Arg
    {
        $var = new Variable(static::THIS_KEY);
        $methodName = $this->classMetadataTransfer->getTargetMethodNameOrFail();
        $methodCall = new MethodCall($var, $methodName);

        return new Arg($methodCall);
    }

    /**
     * @return int
     */
    protected function successfullyProcessed(): int
    {
        $this->methodFound = false;

        return NodeTraverser::DONT_TRAVERSE_CHILDREN;
    }

    protected function getPositionByNamespace(array $calledMethods, string $needleNamespace): ?int
    {
        $position = array_search($this->getMethodNameFromNamespaceName($needleNamespace), $calledMethods, true);
        if ($position === false || is_string($position)) {
            return null;
        }

        return $position;
    }

    /**
     * @param string $namespaceName
     *
     * @return string
     */
    protected function getMethodNameFromNamespaceName(string $namespaceName): string
    {
        $data = explode('::', $namespaceName);

        return end($data);
    }
}
