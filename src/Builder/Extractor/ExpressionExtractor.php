<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use RuntimeException;
use SprykerSdk\Integrator\Builder\Exception\ValueExtractorException;
use SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection;
use SprykerSdk\Integrator\Transfer\ChainAssignValueTransfer;

class ExpressionExtractor implements ExpressionExtractorInterface
{
    protected ValueExtractorStrategyCollection $valueExtractorStrategyCollection;

    /**
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     */
    public function __construct(ValueExtractorStrategyCollection $valueExtractorStrategyCollection)
    {
        $this->valueExtractorStrategyCollection = $valueExtractorStrategyCollection;
    }

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     *
     * @throws \RuntimeException
     *
     * @return array<string, string>
     */
    public function extractExpressions(array $syntaxTree): array
    {
        $expressions = [];
        /** @var \PhpParser\Node\Stmt\Expression $expression */
        foreach ($syntaxTree as $expression) {
            if (!$this->isConfigAssignStatement($expression)) {
                continue;
            }

            /** @var \PhpParser\Node\Expr\Assign $assignExpression */
            $assignExpression = $expression->expr;
            $chainAssignValue = $this->extractChainAssignValue($assignExpression, new ChainAssignValueTransfer());

            if ($chainAssignValue->getValue() === null) {
                throw new RuntimeException(
                    sprintf('Chain value is not found for conf chain %s', implode(', ', $chainAssignValue->getKeys())),
                );
            }

            foreach ($chainAssignValue->getKeys() as $key) {
                try {
                    if (!isset($expressions[$key]) || !is_array($expressions[$key])) {
                        $expressions[$key] = $this->getValue($chainAssignValue->getValue());

                        continue;
                    }
                    $expressions[$key] = array_merge($expressions[$key], $this->getValue($chainAssignValue->getValue()));
                } catch (ValueExtractorException $exception) {
                    continue;
                }
            }
        }

        return $expressions;
    }

    /**
     * @param \PhpParser\Node\Expr\Assign $expression
     * @param \SprykerSdk\Integrator\Transfer\ChainAssignValueTransfer $chainAssignValueDto
     *
     * @return \SprykerSdk\Integrator\Transfer\ChainAssignValueTransfer
     */
    protected function extractChainAssignValue(Assign $expression, ChainAssignValueTransfer $chainAssignValueDto): ChainAssignValueTransfer
    {
        /** @var \PhpParser\Node\Expr\ArrayDimFetch $arrayDimFetchExpression */
        $arrayDimFetchExpression = $expression->var;
        /** @var \PhpParser\Node\Expr\ClassConstFetch $constant */
        $constant = $arrayDimFetchExpression->dim;

        $chainAssignValueDto->addKey(sprintf('\%s::%s', $constant->class->toString(), $constant->name->toString()));

        if ($expression->expr instanceof Assign && $expression->expr->var instanceof ArrayDimFetch) {
            return $this->extractChainAssignValue($expression->expr, $chainAssignValueDto);
        }

        $chainAssignValueDto->setValue($expression->expr);

        return $chainAssignValueDto;
    }

    /**
     * @param \PhpParser\Node $statement
     *
     * @return bool
     */
    protected function isConfigAssignStatement(Node $statement): bool
    {
        return $statement instanceof Expression
            && $statement->expr instanceof Assign
            && $statement->expr->var instanceof ArrayDimFetch
            && $statement->expr->var->dim instanceof ClassConstFetch
            && $statement->expr->var->var instanceof Variable
            && $statement->expr->var->var->name === 'config';
    }

    /**
     * @param \PhpParser\Node\Expr $valueExpression
     *
     * @return mixed
     */
    protected function getValue(Expr $valueExpression)
    {
        $extractedValueObject = $this->valueExtractorStrategyCollection->execute($valueExpression);

        $value = $extractedValueObject->getValue();
        if ($extractedValueObject->isLiteral()) {
            $value = [
                'value' => $extractedValueObject->getValue(),
                'is_literal' => true,
            ];
        }

        return $value;
    }
}
