<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Business\Builder\ClassModifier;

use Shared\Transfer\ClassInformationTransfer;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use RuntimeException;
use SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder;
use SprykerSdk\Integrator\Business\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Business\Builder\Visitor\MethodBodyExtendVisitor;
use SprykerSdk\Integrator\Business\Builder\Visitor\RemoveGlueRelationshipFromClassListVisitor;
use SprykerSdk\Integrator\Business\Helper\ClassHelper;

class GlueRelationshipModifier
{
    /**
     * @var \PhpParser\NodeTraverser
     */
    protected $nodeTraverser;

    /**
     * @var \SprykerSdk\Integrator\Business\Builder\ClassModifier\CommonClassModifier
     */
    protected $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder
     */
    protected $classNodeFinder;

    /**
     * @param \PhpParser\NodeTraverser $nodeTraverser
     * @param \SprykerSdk\Integrator\Business\Builder\ClassModifier\CommonClassModifier $commonClassModifier
     * @param \SprykerSdk\Integrator\Business\Builder\Finder\ClassNodeFinder $classNodeFinder
     */
    public function __construct(
        NodeTraverser $nodeTraverser,
        CommonClassModifier $commonClassModifier,
        ClassNodeFinder $classNodeFinder
    ) {
        $this->nodeTraverser = $nodeTraverser;
        $this->commonClassModifier = $commonClassModifier;
        $this->classNodeFinder = $classNodeFinder;
    }

    /**
     * @param \Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $key
     * @param string $classNameToAdd
     *
     * @return \Shared\Transfer\ClassInformationTransfer
     */
    public function wireGlueRelationship(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $key,
        string $classNameToAdd
    ): ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            $classInformationTransfer = $this->commonClassModifier->overrideMethodFromParent($classInformationTransfer, $targetMethodName);
            $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        }

        if (count($methodNode->params) !== 1) {
            throw new RuntimeException('GLUE relationship method is not valid!');
        }

        if ($this->isRelationshipExists($methodNode, $key, $classNameToAdd)) {
            return $classInformationTransfer;
        }

        $classHelper = new ClassHelper();

        [$keyClass, $keyConst] = explode('::', $key);

        $this->nodeTraverser->addVisitor(new AddUseVisitor($classNameToAdd));
        $this->nodeTraverser->addVisitor(new AddUseVisitor($keyClass));

        $builderFactory = new BuilderFactory();

        $methodBody = [
            new Expression(
                $builderFactory->methodCall(
                    $methodNode->params[0]->var,
                    'addRelationship',
                    $builderFactory->args(
                        [
                            new Arg($builderFactory->classConstFetch($classHelper->getShortClassName($keyClass), $keyConst)),
                            new Arg($builderFactory->new($classHelper->getShortClassName($classNameToAdd))),
                        ]
                    )
                )
            ),
        ];

        $this->nodeTraverser->addVisitor(new MethodBodyExtendVisitor($targetMethodName, $methodBody));
        $classInformationTransfer->setClassTokenTree($this->nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param \Shared\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $key
     * @param string $classNameToRemove
     *
     * @throws \RuntimeException
     *
     * @return \Shared\Transfer\ClassInformationTransfer
     */
    public function unwireGlueRelationship(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        string $key,
        string $classNameToRemove
    ): ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        if (!$methodNode) {
            $classInformationTransfer = $this->commonClassModifier->overrideMethodFromParent($classInformationTransfer, $targetMethodName);
            $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $targetMethodName);
        }

        if (count($methodNode->params) !== 1) {
            throw new RuntimeException('GLUE relationship method is not valid!');
        }

        if (!$this->isRelationshipExists($methodNode, $key, $classNameToRemove)) {
            return $classInformationTransfer;
        }

        [$keyClass, $keyConst] = explode('::', $key);

        $this->nodeTraverser->addVisitor(new RemoveGlueRelationshipFromClassListVisitor($targetMethodName, $keyClass, $keyConst, $classNameToRemove));
        $classInformationTransfer->setClassTokenTree($this->nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $classMethod
     * @param string $key
     * @param string $classNameToAdd
     *
     * @return bool
     */
    protected function isRelationshipExists(ClassMethod $classMethod, string $key, string $classNameToAdd): bool
    {
        $key = ltrim($key, '\\');
        $classNameToAdd = ltrim($classNameToAdd, '\\');
        /** @var \PhpParser\Node\Stmt\ClassMethod|null $node */
        $node = (new NodeFinder())->findFirst(
            $classMethod->stmts,
            function (Node $node) use ($key, $classNameToAdd) {
                if (!($node instanceof MethodCall) || count($node->args) !== 2) {
                    return false;
                }
                if (!($node->args[0]->value instanceof ClassConstFetch)
                    || $node->args[0]->value->class->toString() . '::' . $node->args[0]->value->name !== $key
                ) {
                    return false;
                }

                return ($node->args[1]->value instanceof New_) && $node->args[1]->value->class->toString() === $classNameToAdd;
            }
        );

        return (bool)$node;
    }
}
