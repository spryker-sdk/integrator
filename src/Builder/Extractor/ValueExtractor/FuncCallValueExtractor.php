<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class FuncCallValueExtractor extends AbstractValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof FuncCall;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(
        Expr $expression,
        ValueExtractorStrategyCollection $valueExtractorStrategyCollection
    ): ExtractedValueTransfer {
        $funcName = $expression->name->toString();

        $args = [];
        foreach ($expression->args as $arg) {
            $args[] = $this->getQuotedValueExpression($arg->value, $valueExtractorStrategyCollection);
        }

        return new ExtractedValueTransfer(
            sprintf('%s(%s)', $funcName, $this->recursiveImplodeArguments($args)),
            true,
        );
    }

    /**
     * @param array<array-key, mixed> $arguments
     *
     * @return string
     */
    protected function recursiveImplodeArguments(array $arguments): string
    {
        $implodeArguments = [];
        foreach ($arguments as $argumentKey => $argumentValue) {
            if (is_array($argumentValue)) {
                $argumentValue = sprintf('[%s]', $this->recursiveImplodeArguments($argumentValue));
            }
            if (is_int($argumentKey)) {
                $implodeArguments[] = $argumentValue;

                continue;
            }
            $implodeArguments[] = sprintf('%s => %s', $argumentKey, $argumentValue);
        }

        return implode(', ', $implodeArguments);
    }
}
