<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodChecker;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Builder\Visitor\AddMethodVisitor;
use SprykerSdk\Integrator\Builder\Visitor\CloneNodeWithClearPositionVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemoveMethodVisitor;
use SprykerSdk\Integrator\Builder\Visitor\ReplaceNodeStmtByNameVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class CommonClassModifier
{
    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder
     */
    protected $classNodeFinder;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinder $classNodeFinder
     */
    public function __construct(ClassNodeFinder $classNodeFinder)
    {
        $this->classNodeFinder = $classNodeFinder;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function overrideMethodFromParent(ClassInformationTransfer $classInformationTransfer, string $targetMethodName): ClassInformationTransfer
    {
        $parentClassType = $classInformationTransfer->getParent();

        if (!$parentClassType) {
            return $classInformationTransfer;
        }

        $methodAst = $this->classNodeFinder->findMethodNode($parentClassType, $targetMethodName);

        if (!$methodAst) {
            return $classInformationTransfer;
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new CloneNodeWithClearPositionVisitor());
        /** @var \PhpParser\Node\Stmt\ClassMethod $methodAst */
        $methodAst = $nodeTraverser->traverse([$methodAst])[0];

        $methodBody = [];
        if ((new ClassMethodChecker())->isMethodReturnArray($methodAst)) {
            $builder = new BuilderFactory();
            $methodBody = [new Return_(new Array_())];
            if (!$this->isMethodReturnArrayEmpty($methodAst)) {
                $methodBody = [new Return_(
                    $builder->funcCall('array_merge', [
                                       new Arg(new StaticCall(
                                           new Name('parent'),
                                           $targetMethodName
                                       )),
                                       new Arg(new Array_()),
                    ])
                )];
            }
        } elseif (count($methodAst->params) === 1) {
            $methodBody = [new Return_($methodAst->params[]->var)];
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new ReplaceNodeStmtByNameVisitor($targetMethodName, $methodBody));
        $methodAst = $nodeTraverser->traverse([$methodAst])[0];

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new AddMethodVisitor($methodAst));
        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param \PhpParser\Node[] $methodAst
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function replaceMethodBody(ClassInformationTransfer $classInformationTransfer, string $targetMethodName, array $methodAst): ClassInformationTransfer
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new ReplaceNodeStmtByNameVisitor($targetMethodName, $methodAst));
        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodNameToRemove
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function removeClassMethod(ClassInformationTransfer $classInformationTransfer, string $methodNameToRemove): ClassInformationTransfer
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new RemoveMethodVisitor($methodNameToRemove));
        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodName
     * @param bool|int|float|string|array|null $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setMethodReturnValue(ClassInformationTransfer $classInformationTransfer, string $methodName, $value): ClassInformationTransfer
    {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $methodName);
        if (!$methodNode) {
            $classInformationTransfer = $this->overrideMethodFromParent($classInformationTransfer, $methodName);
        }

        $methodBody = [new Return_((new BuilderFactory())->val($value))];

        return $this->replaceMethodBody($classInformationTransfer, $methodName, $methodBody);
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    protected function isMethodReturnArrayEmpty(ClassMethod $node): bool
    {
        /** @var \PhpParser\Node\Expr\Array_|null $arrayNode */
        $arrayNode = (new NodeFinder())->findFirst($node->stmts, function (Node $node) {
            return $node instanceof Array_;
        });

        return $arrayNode && !count($arrayNode->items);
    }
}
