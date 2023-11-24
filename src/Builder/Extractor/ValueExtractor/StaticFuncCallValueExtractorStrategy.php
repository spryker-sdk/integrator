<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class StaticFuncCallValueExtractorStrategy extends AbstractValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof StaticCall;
    }

    /**
     * @param \PhpParser\Node\Expr\StaticCall $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(
        Expr $expression,
        ValueExtractorStrategyCollection $valueExtractorStrategyCollection
    ): ExtractedValueTransfer {
        $arguments = [];
        if (!empty($expression->args)) {
            foreach ($expression->args as $argument) {
                $argumentValue = $valueExtractorStrategyCollection->execute($argument->value)->getValue();
                if (is_bool($argumentValue)) {
                    $argumentValue = var_export($argumentValue, true);
                }
                if (is_array($argumentValue)) {
                    $argumentValue = $this->createArrayStringFromResult($argumentValue);
                }
                $arguments[] = $argumentValue;
            }
        }

        /** @var \PhpParser\Node\Name $class */
        $class = $expression->class;
        /** @var \PhpParser\Node\Identifier $name */
        $name = $expression->name;

        return new ExtractedValueTransfer(
            sprintf('%s::%s(%s)', $this->getCalleeName($class->parts), $name->name, implode(', ', $arguments)),
            true,
        );
    }

    /**
     * @param array<string> $classParts
     *
     * @return string
     */
    protected function getCalleeName(array $classParts): string
    {
        $isClassName = $classParts[0] !== strtolower($classParts[0]);

        return $isClassName ? '\\' . implode('\\', $classParts) : $classParts[0];
    }
}
