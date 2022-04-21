<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException;
use SprykerSdk\Integrator\Builder\Visitor\AddMethodVisitor;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class MethodCreator extends AbstractMethodCreator implements MethodCreatorInterface
{
    /**
     * @var int
     */
    protected const SINGLE_TREE_RETURN_STATEMENT_COUNT_ELEMENTS = 1;

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
     * @param \SprykerSdk\Integrator\Builder\Creator\MethodStatementsCreatorInterface $methodStatementsCreator
     * @param \SprykerSdk\Integrator\Builder\Creator\MethodDocBlockCreatorInterface $methodDocBlockCreator
     * @param \SprykerSdk\Integrator\Builder\Creator\MethodReturnTypeCreatorInterface $methodReturnTypeCreator
     * @param \PhpParser\ParserFactory $parserFactory
     */
    public function __construct(
        MethodStatementsCreatorInterface $methodStatementsCreator,
        MethodDocBlockCreatorInterface $methodDocBlockCreator,
        MethodReturnTypeCreatorInterface $methodReturnTypeCreator,
        ParserFactory $parserFactory
    ) {
        $this->methodStatementsCreator = $methodStatementsCreator;
        $this->methodDocBlockCreator = $methodDocBlockCreator;
        $this->methodReturnTypeCreator = $methodReturnTypeCreator;
        $this->parserFactory = $parserFactory;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array|string|float|int|bool|null $value
     *
     * @throws \SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException
     *
     * @return array<array-key, \PhpParser\Node\Stmt>
     */
    public function createMethodBody(ClassInformationTransfer $classInformationTransfer, $value): array
    {
        if (is_array($value)) {
            return $this->createReturnArrayStatement($classInformationTransfer, $value);
        }

        $preparedValue = $this->createPreparedValueForParser($value);
        $tree = $this->parserFactory->create(ParserFactory::PREFER_PHP7)->parse($preparedValue);
        if (!$tree) {
            throw new LiteralValueParsingException(sprintf('Value is not valid PHP code: `%s`', $value));
        }

        if (!$this->isSingleReturnStatement($tree)) {
            return $this->createMultiStatementMethodBody($tree);
        }

        return $this->createSingleStatementMethodBody($classInformationTransfer, $tree, $value);
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
        $returnType = $this->methodReturnTypeCreator->createMethodReturnType($value);
        $classMethod = new ClassMethod(
            $methodName,
            ['flags' => Class_::MODIFIER_PUBLIC, 'returnType' => $returnType],
            ['comments' => [$this->methodDocBlockCreator->createMethodDocBlock($value)]],
        );
        $nodeTraverser->addVisitor(new AddMethodVisitor($classMethod));

        return $classInformationTransfer
            ->setClassTokenTree($nodeTraverser->traverse($classInformationTransfer->getClassTokenTree()));
    }

    /**
     * @return \PhpParser\Parser
     */
    protected function createParser(): Parser
    {
        return (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
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
     * @param array<\PhpParser\Node\Stmt> $tree
     *
     * @return array
     */
    protected function createMultiStatementMethodBody(array $tree): array
    {
        $lastElement = $tree[count($tree) - 1];
        if (!$lastElement instanceof Return_ && property_exists($lastElement, 'expr')) {
            $tree[count($tree) - 1] = new Return_($lastElement->expr);
        }

        return $tree;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param array $tree
     * @param mixed $value
     *
     * @throws \SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException
     *
     * @return array
     */
    protected function createSingleStatementMethodBody(ClassInformationTransfer $classInformationTransfer, array $tree, $value): array
    {
        /** @var \PhpParser\Node\Stmt\Expression|null $returnExpression */
        $returnExpression = $tree[0] ?? null;
        if (!$returnExpression) {
            throw new LiteralValueParsingException(sprintf('Value is not valid PHP code: `%s`', $value));
        }
        if (property_exists($returnExpression->expr, 'class')) {
            return $this->createClassConstantReturnStatement($classInformationTransfer, $returnExpression);
        }

        return [new Return_($returnExpression->expr)];
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \PhpParser\Node\Stmt\Expression $expression
     *
     * @return array
     */
    protected function createClassConstantReturnStatement(
        ClassInformationTransfer $classInformationTransfer,
        Expression $expression
    ): array {
        /** @var \PhpParser\Node\Expr\ClassConstFetch $expressionStatement */
        $expressionStatement = $expression->expr;
        /** @var \PhpParser\Node\Identifier $expressionName */
        $expressionName = $expressionStatement->name;
        /** @var \PhpParser\Node\Name\FullyQualified $expressionClass */
        $expressionClass = $expressionStatement->class;
        $returnExpressionClass = $this->getShortClassNameAndAddToClassInformation(
            $classInformationTransfer,
            implode('\\', $expressionClass->parts) . '::' . $expressionName->name,
        );
        $returnExpressionClassParts = explode('::', $returnExpressionClass);
        $expressionClass->parts = [$returnExpressionClassParts[0]];
        $returnClassConstExpression = $this->createClassConstantExpression(
            $returnExpressionClassParts[0],
            $returnExpressionClassParts[1],
        );

        return [new Return_($returnClassConstExpression)];
    }
}
