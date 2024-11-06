<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Transfer\CallMetadataTransfer;
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
            $this->addNewCallIntoArrayMergeFuncNode($node, $callMetadataTransfer);

            return $this->successfullyProcessed();
        }

        if ($node instanceof Array_) {
            $this->addNewCallIntoArray($node, $callMetadataTransfer);

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
        return $node->name instanceof Name && $node->name->name === static::ARRAY_MERGE_FUNCTION;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     * @param \SprykerSdk\Integrator\Transfer\CallMetadataTransfer $callMetadataTransfer
     *
     * @return \PhpParser\Node
     */
    protected function addNewCallIntoArrayMergeFuncNode(FuncCall $node, CallMetadataTransfer $callMetadataTransfer): Node
    {
        $calledMethods = $this->getCalledMethodsFromArrayMergeFunc($node);
        if ($this->hasTargetMethodCalled($calledMethods)) {
            return $node;
        }

        if ($this->classMetadataTransfer->getCall() && $this->classMetadataTransfer->getCall()->getIndex()) {
            $node->args[] = $this->createFunctionArgWithMethodCall();

            return $node;
        }

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
     * @param \PhpParser\Node $node
     *
     * @return array<string|int, string>
     */
    protected function getCalledMethodsFromArray(Node $node): array
    {
        $result = [];

        $nodeFinder = new NodeFinder();
        /** @var array<\PhpParser\Node\Expr\ArrayItem> $arrayItems */
        $arrayItems = $nodeFinder->findInstanceOf($node, ArrayItem::class);
        foreach ($arrayItems as $arrayItem) {
            if (!$arrayItem->value instanceof MethodCall) {
                continue;
            }
            /** @var \PhpParser\Node\Identifier $methodName */
            $methodName = $arrayItem->value->name;

            if (!$arrayItem->key instanceof String_) {
                $result[] = $methodName->name;

                continue;
            }

            $result[$arrayItem->key->value] = $methodName->name;
        }

        return $result;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return array<string|int, string>
     */
    protected function getCalledMethodsFromArrayMergeFunc(Node $node): array
    {
        $result = [];
        $nodeFinder = new NodeFinder();
        $args = $nodeFinder->findInstanceOf($node, Arg::class);
        /** @var \PhpParser\Node\Arg $arg */
        foreach ($args as $arg) {
            if ($arg->value instanceof Array_) {
                $calledMethodsFromArray = $this->getCalledMethodsFromArray($arg->value);
                $result = array_merge($result, $calledMethodsFromArray);
            }

            if ($arg->value instanceof MethodCall) {
                /** @var \PhpParser\Node\Identifier $methodName */
                $methodName = $arg->value->name;
                $result[] = $methodName->name;
            }
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

        $index = $this->classMetadataTransfer->getCall() ? $this->classMetadataTransfer->getCall()->getIndex() : null;
        if (!$index) {
            return new Arg($methodCall);
        }

        $arrayItem = new ArrayItem($methodCall, new String_($index));
        $array = new Array_([$arrayItem]);

        return new Arg($array);
    }

    /**
     * @return int
     */
    protected function successfullyProcessed(): int
    {
        $this->methodFound = false;

        return NodeTraverser::DONT_TRAVERSE_CHILDREN;
    }

    /**
     * @param array<string> $calledMethods
     * @param string $needleNamespace
     *
     * @return int|null
     */
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

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     * @param \SprykerSdk\Integrator\Transfer\CallMetadataTransfer $callMetadataTransfer
     *
     * @return \PhpParser\Node
     */
    protected function addNewCallIntoArray(Array_ $node, CallMetadataTransfer $callMetadataTransfer): Node
    {
        $calledMethods = $this->getCalledMethodsFromArray($node);
        if ($this->hasTargetMethodCalled($calledMethods)) {
            return $node;
        }

        $before = $callMetadataTransfer->getBefore();
        if ($before) {
            $beforePosition = $this->getPositionByNamespace($calledMethods, $before);
            if ($beforePosition !== null) {
                return $this->addNewCallIntoArrayNodeByPosition($node, $beforePosition);
            }
        }

        $after = $callMetadataTransfer->getAfter();
        if ($after) {
            $afterPosition = $this->getPositionByNamespace($calledMethods, $after);
            if ($afterPosition !== null) {
                return $this->addNewCallIntoArrayNodeByPosition($node, $afterPosition + 1);
            }
        }

        $node->items[] = $this->createArrayItemWithMethodCall();

        return $node;
    }

    /**
     * @param array<string|int, string> $calledMethods
     *
     * @return bool
     */
    protected function hasTargetMethodCalled(array $calledMethods): bool
    {
        foreach ($calledMethods as $key => $method) {
            if (is_string($key)) {
                $index = $this->classMetadataTransfer->getCall() ? $this->classMetadataTransfer->getCall()->getIndex() : null;

                return $method === $this->classMetadataTransfer->getTargetMethodNameOrFail() && $key === $index;
            }
            if ($method === $this->classMetadataTransfer->getTargetMethodNameOrFail()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     * @param int $position
     *
     * @return \PhpParser\Node
     */
    protected function addNewCallIntoArrayNodeByPosition(Array_ $node, int $position): Node
    {
        array_splice($node->items, $position, 0, [$this->createArrayItemWithMethodCall()]);

        return $node;
    }

    /**
     * @return \PhpParser\Node\ArrayItem
     */
    protected function createArrayItemWithMethodCall(): ArrayItem
    {
        $var = new Variable(static::THIS_KEY);
        $methodName = $this->classMetadataTransfer->getTargetMethodNameOrFail();
        $methodCall = new MethodCall($var, $methodName);

        $index = $this->classMetadataTransfer->getCall() ? $this->classMetadataTransfer->getCall()->getIndex() : null;
        if ($index) {
            return new ArrayItem($methodCall, new String_($index));
        }

        return new ArrayItem($methodCall);
    }
}
