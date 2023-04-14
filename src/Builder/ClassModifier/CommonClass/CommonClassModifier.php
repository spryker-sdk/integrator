<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\CommonClass;

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
use SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface;
use SprykerSdk\Integrator\Builder\Comparer\NodeComparerFactory;
use SprykerSdk\Integrator\Builder\Creator\MethodCreatorInterface;
use SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreatorInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddMethodVisitor;
use SprykerSdk\Integrator\Builder\Visitor\AddStatementToStatementListVisitor;
use SprykerSdk\Integrator\Builder\Visitor\CloneNodeWithClearPositionVisitor;
use SprykerSdk\Integrator\Builder\Visitor\RemoveMethodVisitor;
use SprykerSdk\Integrator\Builder\Visitor\ReplaceNodePropertiesByNameVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class CommonClassModifier implements CommonClassModifierInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    protected $classNodeFinder;

    /**
     * @var \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface
     */
    protected $classMethodChecker;

    /**
     * @var \SprykerSdk\Integrator\Builder\Creator\MethodCreatorInterface
     */
    protected $methodCreator;

    /**
     * @var \SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreatorInterface
     */
    protected $methodStatementsCreator;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \SprykerSdk\Integrator\Builder\Checker\ClassMethodCheckerInterface $classMethodChecker
     * @param \SprykerSdk\Integrator\Builder\Creator\MethodCreatorInterface $methodCreator
     * @param \SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreatorInterface $methodStatementsCreator
     */
    public function __construct(
        ClassNodeFinderInterface $classNodeFinder,
        ClassMethodCheckerInterface $classMethodChecker,
        MethodCreatorInterface $methodCreator,
        MethodStatementsCreatorInterface $methodStatementsCreator
    ) {
        $this->classNodeFinder = $classNodeFinder;
        $this->classMethodChecker = $classMethodChecker;
        $this->methodCreator = $methodCreator;
        $this->methodStatementsCreator = $methodStatementsCreator;
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

        $methodSyntaxTree = $this->classNodeFinder->findMethodNode($parentClassType, $targetMethodName);

        if (!$methodSyntaxTree) {
            return $classInformationTransfer;
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new CloneNodeWithClearPositionVisitor());

        /** @var \PhpParser\Node\Stmt\ClassMethod $methodSyntaxTree */
        $methodSyntaxTree = $nodeTraverser->traverse([$methodSyntaxTree])[0];

        $methodBody = [];
        if ($this->classMethodChecker->isMethodReturnArray($methodSyntaxTree)) {
            $methodBody = $this->buildMethodBodyToReturnArray($targetMethodName, $methodSyntaxTree);
        } elseif (count($methodSyntaxTree->params) === 1) {
            $methodBody = [new Return_($methodSyntaxTree->params[0]->var)];
        }

        $nodeTraverser = new NodeTraverser();
        $properties = [ReplaceNodePropertiesByNameVisitor::STMTS => $methodBody];
        $nodeTraverser->addVisitor(new ReplaceNodePropertiesByNameVisitor($targetMethodName, $properties));
        $methodSyntaxTree = $nodeTraverser->traverse([$methodSyntaxTree])[0];

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new AddMethodVisitor($methodSyntaxTree));
        $classInformationTransfer->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param string $targetMethodName
     * @param \PhpParser\Node\Stmt\ClassMethod $methodSyntaxTree
     *
     * @return array<\PhpParser\Node\Stmt\Return_>
     */
    protected function buildMethodBodyToReturnArray(string $targetMethodName, ClassMethod $methodSyntaxTree): array
    {
        $builder = new BuilderFactory();
        $methodBody = [new Return_(new Array_())];
        if ($this->isMethodReturnArrayEmpty($methodSyntaxTree)) {
            return $methodBody;
        }

        return [new Return_(
            $builder->funcCall('array_merge', [
                new Arg(new StaticCall(
                    new Name('parent'),
                    $targetMethodName,
                )),
                new Arg(new Array_()),
            ]),
        )];
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetMethodName
     * @param array<string, mixed> $methodNodeProperties
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function replaceMethodBody(
        ClassInformationTransfer $classInformationTransfer,
        string $targetMethodName,
        array $methodNodeProperties = []
    ): ClassInformationTransfer {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new ReplaceNodePropertiesByNameVisitor($targetMethodName, $methodNodeProperties));
        $classInformationTransfer
            ->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

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
        $classInformationTransfer
            ->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));

        return $classInformationTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodName
     * @param array|string|float|int|bool|null $value
     * @param bool $isLiteral
     * @param mixed $previousValue
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function createClassMethod(
        ClassInformationTransfer $classInformationTransfer,
        string $methodName,
        $value,
        bool $isLiteral,
        $previousValue
    ): ClassInformationTransfer {
        $methodNode = $this->classNodeFinder->findMethodNode($classInformationTransfer, $methodName);

        if (!$isLiteral && $methodNode && is_array($value)) {
            return $this->appendNonLiteralArrayValueToMethodBody(
                $classInformationTransfer,
                $methodName,
                $value,
            );
        }
        if (!$methodNode) {
            $classInformationTransfer = $this->methodCreator
                ->createMethod($classInformationTransfer, $methodName, $value);
        }
        if (!$this->classMethodChecker->isMethodNodeSameAsValue($methodNode, $previousValue)) {
            return $classInformationTransfer;
        }
        $methodBody = $this->methodCreator->createMethodBody($classInformationTransfer, $value);

        $methodNodeProperties = [ReplaceNodePropertiesByNameVisitor::STMTS => $methodBody];

        return $this->replaceMethodBody($classInformationTransfer, $methodName, $methodNodeProperties);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    protected function appendNonLiteralArrayValueToMethodBody(
        ClassInformationTransfer $classInformationTransfer,
        string $methodName,
        $value
    ): ClassInformationTransfer {
        $arrayItems = $this->methodStatementsCreator->createMethodStatementsFromValue($classInformationTransfer, $value);
        $nodeTraverser = new NodeTraverser();
        $nodeComparer = (new NodeComparerFactory())->createNodeComparer();
        $nodeTraverser->addVisitor(new AddStatementToStatementListVisitor($methodName, $arrayItems, $nodeComparer));

        return $classInformationTransfer
                ->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    protected function isMethodReturnArrayEmpty(ClassMethod $node): bool
    {
        /** @var array<\PhpParser\Node> $nodes */
        $nodes = $node->stmts;

        /** @var \PhpParser\Node\Expr\Array_|null $arrayNode */
        $arrayNode = (new NodeFinder())->findFirst($nodes, function (Node $node) {
            return $node instanceof Array_;
        });

        return $arrayNode && !count($arrayNode->items);
    }
}
