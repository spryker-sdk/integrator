<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\Comment\Doc;
use PhpParser\Modifiers;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException;
use SprykerSdk\Integrator\Builder\Exception\NotFoundReturnExpressionException;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolverInterface;
use SprykerSdk\Integrator\Builder\Visitor\AddMethodVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class MethodCreator extends AbstractMethodCreator implements MethodCreatorInterface
{
    /**
     * @var int
     */
    protected const SINGLE_TREE_RETURN_STATEMENT_COUNT_ELEMENTS = 1;

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    protected ClassNodeFinderInterface $classNodeFinder;

    /**
     * @var \SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreatorInterface
     */
    protected $methodStatementsCreator;

    /**
     * @var \SprykerSdk\Integrator\Builder\Creator\MethodDocBlockCreatorInterface
     */
    protected $methodDocBlockCreator;

    /**
     * @var \SprykerSdk\Integrator\Builder\Creator\MethodReturnTypeCreatorInterface
     */
    protected $methodReturnTypeCreator;

    /**
     * @var \PhpParser\ParserFactory
     */
    protected $parserFactory;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreatorInterface $methodStatementsCreator
     * @param \SprykerSdk\Integrator\Builder\Creator\MethodDocBlockCreatorInterface $methodDocBlockCreator
     * @param \SprykerSdk\Integrator\Builder\Creator\MethodReturnTypeCreatorInterface $methodReturnTypeCreator
     * @param \PhpParser\ParserFactory $parserFactory
     * @param \SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolverInterface $prefixedConstNameResolver
     */
    public function __construct(
        ClassNodeFinderInterface $classNodeFinder,
        MethodStatementsCreatorInterface $methodStatementsCreator,
        MethodDocBlockCreatorInterface $methodDocBlockCreator,
        MethodReturnTypeCreatorInterface $methodReturnTypeCreator,
        ParserFactory $parserFactory,
        PrefixedConstNameResolverInterface $prefixedConstNameResolver
    ) {
        parent::__construct($prefixedConstNameResolver);

        $this->classNodeFinder = $classNodeFinder;
        $this->methodStatementsCreator = $methodStatementsCreator;
        $this->methodDocBlockCreator = $methodDocBlockCreator;
        $this->methodReturnTypeCreator = $methodReturnTypeCreator;
        $this->parserFactory = $parserFactory;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array|string|float|int|bool|null $value
     * @param bool $isLiteral
     *
     * @throws \SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException
     *
     * @return array<array-key, \PhpParser\Node\Stmt>
     */
    public function createMethodBody(ClassInformationTransfer $classInformationTransfer, $value, bool $isLiteral = false): array
    {
        if (is_array($value)) {
            return $this->createReturnArrayStatement($classInformationTransfer, $value);
        }

        $preparedValue = $this->createPreparedValueForParser($value);
        $tree = $this->parserFactory->createForHostVersion()->parse($preparedValue);
        if (!$tree) {
            throw new LiteralValueParsingException(sprintf('Value is not valid PHP code: `%s`', $value));
        }

        if ($this->isSingleReturnStatement($tree)) {
            return $this->createSingleStatementMethodBody($classInformationTransfer, $tree, $value, $isLiteral);
        }

        return $tree;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function createPreparedValueForParser($value): string
    {
        if (is_bool($value)) {
            $value = var_export($value, true);
        }

        return sprintf('<?php %s;', $value);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $methodName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function createMethod(
        ClassInformationTransfer $classInformationTransfer,
        string $methodName,
        $value
    ): ClassInformationTransfer {
        $nodeTraverser = new NodeTraverser();
        $parentReturnType = null;
        $parentClassMethod = null;
        if ($classInformationTransfer->getParent()) {
            $parentClassMethod = $this->classNodeFinder->findMethodNode($classInformationTransfer->getParent(), $methodName);
            $parentReturnType = $parentClassMethod ? $this->getReturnType($parentClassMethod) : null;
        }
        $returnType = $parentReturnType ?: $this->methodReturnTypeCreator->createMethodReturnType($value);
        $flags = $parentClassMethod ? $this->getModifierFromClassMethod($parentClassMethod) : Modifiers::PUBLIC;
        $docType = $parentClassMethod && $parentClassMethod->getDocComment() ?
            new Doc($parentClassMethod->getDocComment()->getText()) :
            $this->methodDocBlockCreator->createMethodDocBlock($value);

        $classMethod = new ClassMethod(
            $methodName,
            [
                'flags' => $flags,
                'returnType' => $returnType,
            ],
            [
            'comments' =>
                [
                    $docType,
                ],
            ],
        );

        $classMethod->stmts = [];
        $nodeTraverser->addVisitor(new AddMethodVisitor($classMethod));

        return $classInformationTransfer
            ->setTokenTree($nodeTraverser->traverse($classInformationTransfer->getTokenTree()));
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $classMethod
     *
     * @return mixed
     */
    public function getReturnType(ClassMethod $classMethod)
    {
        if (!$classMethod->getReturnType()) {
            return null;
        }
        $returnType = null;
        $parentReturnType = $classMethod->getReturnType();
        $nullable = '';
        if ($parentReturnType instanceof NullableType) {
            $nullable = '?';
            $parentReturnType = $parentReturnType->type;
        }
        if ($parentReturnType instanceof Identifier) {
            return new Identifier($nullable . $parentReturnType->name);
        }
        if ($parentReturnType instanceof FullyQualified) {
            return new FullyQualified($parentReturnType->toString());
        }

        return $returnType;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $classMethod
     *
     * @return int
     */
    protected function getModifierFromClassMethod(ClassMethod $classMethod): int
    {
        return $classMethod->isProtected() ? Modifiers::PROTECTED : Modifiers::PUBLIC;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param mixed $value
     *
     * @return array<\PhpParser\Node\Stmt\Return_>
     */
    protected function createReturnArrayStatement(ClassInformationTransfer $classInformationTransfer, $value): array
    {
        $tree = $this->methodStatementsCreator->createMethodStatementsFromValue($classInformationTransfer, $value);

        return [new Return_(new Array_($tree))];
    }

    /**
     * @param array<\PhpParser\Node\Stmt> $tree
     *
     * @return bool
     */
    protected function isSingleReturnStatement(array $tree): bool
    {
        return $tree && count($tree) === static::SINGLE_TREE_RETURN_STATEMENT_COUNT_ELEMENTS;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array<\PhpParser\Node\Stmt> $tree
     * @param mixed $value
     * @param bool $isLiteral
     *
     * @throws \SprykerSdk\Integrator\Builder\Exception\NotFoundReturnExpressionException
     *
     * @return array<\PhpParser\Node\Stmt\Return_>
     */
    protected function createSingleStatementMethodBody(ClassInformationTransfer $classInformationTransfer, array $tree, $value, bool $isLiteral): array
    {
        /** @var \PhpParser\Node\Stmt\Expression|null $returnExpression */
        $returnExpression = $tree[0] ?? null;
        if (!$returnExpression) {
            throw new NotFoundReturnExpressionException(sprintf('Not found any statements in value: `%s`', $value));
        }
        if (property_exists($returnExpression->expr, 'class')) {
            return $this->createClassConstantReturnStatement($classInformationTransfer, $returnExpression);
        }

        $returnExpression = !$isLiteral && $returnExpression->expr instanceof ConstFetch && !in_array((string)$returnExpression->expr->name, ['true', 'false'], true)
            ? new String_((string)$returnExpression->expr->name)
            : $returnExpression->expr;

        return [new Return_($returnExpression)];
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \PhpParser\Node\Stmt\Expression $expression
     *
     * @return array<\PhpParser\Node\Stmt\Return_>
     */
    protected function createClassConstantReturnStatement(ClassInformationTransfer $classInformationTransfer, Expression $expression): array
    {
        /** @var \PhpParser\Node\Expr\ClassConstFetch $expressionStatement */
        $expressionStatement = $expression->expr;
        /** @var \PhpParser\Node\Identifier $expressionName */
        $expressionName = $expressionStatement->name;
        /** @var \PhpParser\Node\Name\FullyQualified $expressionClass */
        $expressionClass = $expressionStatement->class;
        $expressionClass->name = (new Name([$expressionClass->toString()]))->name;
        $returnClassConstExpression = $this->createClassConstantExpression(
            $classInformationTransfer,
            $expressionClass->toString(),
            $expressionName->name,
        );

        return [new Return_($returnClassConstExpression)];
    }
}
