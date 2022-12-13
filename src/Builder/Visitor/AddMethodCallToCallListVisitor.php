<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
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
    public const THIS = 'this';

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

        $callTarget = explode('::', $callMetadataTransfer->getTargetOrFail());
        if ($node instanceof ClassMethod && $node->name->toString() === end($callTarget)) {
            $this->methodFound = true;

            return $node;
        }

        if ($this->methodFound) {
            if ($node instanceof FuncCall && $this->isArrayMergeFuncCallNode($node)) {
                $this->addNewCallIntoArrayMergeFuncNode($node);

                return $this->successfullyProcessed();
            }
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
        $node->args[] = $this->createMethodCall();

        return $node;
    }

    /**
     * @return \PhpParser\Node\Expr\MethodCall
     */
    protected function createMethodCall(): MethodCall
    {
        $var = new Variable(static::THIS);
        $name = $this->classMetadataTransfer->getTargetMethodNameOrFail();

        return new MethodCall($var, $name);
    }

    /**
     * @return int
     */
    protected function successfullyProcessed(): int
    {
        $this->methodFound = false;

        return NodeTraverser::DONT_TRAVERSE_CHILDREN;
    }
}
