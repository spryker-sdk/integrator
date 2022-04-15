<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Creator;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use SprykerSdk\Integrator\Builder\Exception\LiteralValueParsingException;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class MethodBodyCreator extends AbstractMethodCreator implements MethodBodyCreatorInterface
{
    /**
     * @var int
     */
    protected const SINGLE_TREE_RETURN_STATEMENT_COUNT_ELEMENTS = 1;

    /**
     * @var \SprykerSdk\Integrator\Builder\Creator\NodeTreeCreatorInterface
     */
    protected $nodeTreeCreator;

    /**
     * @param \SprykerSdk\Integrator\Builder\Creator\NodeTreeCreatorInterface $nodeTreeCreator
     */
    public function __construct(NodeTreeCreatorInterface $nodeTreeCreator)
    {
        $this->nodeTreeCreator = $nodeTreeCreator;
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

        $preparedValue = sprintf('<?php %s;', $value);
        $tree = $this->createParser()->parse($preparedValue);
        if (!$tree) {
            throw new LiteralValueParsingException(sprintf('Value is not valid PHP code: `%s`', $value));
        }

        if (!$this->isSingleReturnStatement($tree)) {
            return $this->createMultiStatementMethodBody($tree);
        }

        return $this->createSingleStatementMethodBody($classInformationTransfer, $tree, $value);
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
        $tree = $this->nodeTreeCreator->createNodeTreeFromValue($classInformationTransfer, $value);

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
