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
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use RuntimeException;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddUseVisitor;
use SprykerSdk\Integrator\Builder\Visitor\MethodBodyExtendVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemoveGlueRelationshipFromClassListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemoveUseVisitor;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class GlueRelationshipModifier implements GlueRelationshipModifierInterface
{
    /**
     * @var \PhpParser\NodeTraverser
     */
    protected $nodeTraverser;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface
     */
    protected $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    protected $classNodeFinder;

    /**
     * @var \SprykerSdk\Integrator\Helper\ClassHelperInterface
     */
    protected $classHelper;

    /**
     * @var \PhpParser\BuilderFactory
     */
    protected $builderFactory;

    /**
     * @param \PhpParser\NodeTraverser $nodeTraverser
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \SprykerSdk\Integrator\Helper\ClassHelperInterface $classHelper
     * @param \PhpParser\BuilderFactory $builderFactory
     */
    public function __construct(
        NodeTraverser $nodeTraverser,
        CommonClassModifierInterface $commonClassModifier,
        ClassNodeFinderInterface $classNodeFinder,
        ClassHelperInterface $classHelper,
        BuilderFactory $builderFactory
    ) {
        $this->nodeTraverser = $nodeTraverser;
        $this->commonClassModifier = $commonClassModifier;
        $this->classNodeFinder = $classNodeFinder;
        $this->builderFactory = $builderFactory;
        $this->classHelper = $classHelper;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $key
     * @param string $classNameToAdd
     *
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
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

        [$keyClass, $keyConst] = explode('::', $key);

        $this->nodeTraverser->addVisitor(new AddUseVisitor($classNameToAdd));
        $this->nodeTraverser->addVisitor(new AddUseVisitor($keyClass));

        $methodBody = $this->getMethodBody($methodNode, $classNameToAdd, $keyClass, $keyConst);

        $this->nodeTraverser->addVisitor(new MethodBodyExtendVisitor($targetMethodName, $methodBody));
        $classInformationTransfer->setClassTokenTree($this->nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $methodNode
     * @param string $classNameToAdd
     * @param string $keyClass
     * @param string $keyConst
     *
     * @return array<\PhpParser\Node\Stmt\Expression>
     */
    protected function getMethodBody(ClassMethod $methodNode, string $classNameToAdd, string $keyClass, string $keyConst): array
    {
        $arguments = [
            new Arg($this->builderFactory->classConstFetch($this->classHelper->getShortClassName($keyClass), $keyConst)),
            new Arg($this->builderFactory->new($this->classHelper->getShortClassName($classNameToAdd))),
        ];

        return [
            new Expression(
                $this->builderFactory->methodCall(
                    $methodNode->params[0]->var,
                    'addRelationship',
                    $this->builderFactory->args($arguments),
                ),
            ),
        ];
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param string $key
     * @param string $classNameToRemove
     *
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
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

        $this->nodeTraverser->addVisitor(new RemoveUseVisitor($keyClass));
        $this->nodeTraverser->addVisitor(new RemoveUseVisitor($classNameToRemove));
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
                if (
                    !($node->args[0]->value instanceof ClassConstFetch)
                    || $node->args[0]->value->class->toString() . '::' . $node->args[0]->value->name !== $key
                ) {
                    return false;
                }

                return ($node->args[1]->value instanceof New_) && $node->args[1]->value->class->toString() === $classNameToAdd;
            },
        );

        return (bool)$node;
    }
}
