<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class ConstFetchValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof ConstFetch;
    }

    /**
     * @param \PhpParser\Node\Expr\ConstFetch $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(
        Expr $expression,
        ValueExtractorStrategyCollection $valueExtractorStrategyCollection
    ): ExtractedValueTransfer {
        $value = $this->filterValue($expression->name->toString());

        return new ExtractedValueTransfer($value, $this->isLiteral($value));
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function filterValue($value): bool
    {
        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        return (bool)$value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isLiteral($value): bool
    {
        return !is_bool($value) && $value !== null;
    }
}
