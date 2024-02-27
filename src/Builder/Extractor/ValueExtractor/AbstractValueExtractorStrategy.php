<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;

class AbstractValueExtractorStrategy
{
    /**
     * @param \PhpParser\Node\Expr $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return string
     */
    protected function getQuotedValueExpression(
        Expr $expression,
        ValueExtractorStrategyCollection $valueExtractorStrategyCollection
    ): string {
        if ($expression instanceof String_) {
            return sprintf('\'%s\'', $expression->value);
        }
        if ($this->isNonVarExportExpression($expression)) {
            $expressionValue = $valueExtractorStrategyCollection->execute($expression)->getValue();
            if (is_bool($expressionValue)) {
                $expressionValue = var_export($expressionValue, true);
            }

            return sprintf('%s', $expressionValue);
        }

        $result = $valueExtractorStrategyCollection->execute($expression)->getValue();
        if (is_array($result)) {
            return $this->createArrayStringFromResult($result);
        }

        if (is_string($result)) {
            return $result;
        }

        return var_export($result, true);
    }

    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    protected function isNonVarExportExpression(Expr $expression): bool
    {
        return $expression instanceof StaticCall
            || $expression instanceof ClassConstFetch
            || $expression instanceof FuncCall
            || $expression instanceof ConstFetch
            || $expression instanceof MethodCall
            || $expression instanceof Variable
            || $expression instanceof Ternary;
    }

    /**
     * @param array<int|string, mixed> $array
     *
     * @return string
     */
    protected function createArrayStringFromResult(array $array): string
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->createArrayStringFromResult($value);
            }
            if (is_int($key)) {
                $result[] = $value;

                continue;
            }
            if (is_string($key) && !$this->isConstant($key)) {
                $key = sprintf('\'%s\'', $key);
            }
            if (is_string($value) && !$this->isConstant($value)) {
                $value = sprintf('\'%s\'', $value);
            }

            $result[] = $key . ' => ' . $value;
        }

        return sprintf('[%s]', implode(', ', $result));
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isConstant(string $value): bool
    {
        return (bool)strpos($value, '::');
    }
}
