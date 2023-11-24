<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class ArrayDimValueExtractor implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof ArrayDimFetch;
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayDimFetch $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(
        Expr $expression,
        ValueExtractorStrategyCollection $valueExtractorStrategyCollection
    ): ExtractedValueTransfer {
        /** @var \PhpParser\Node\Expr\Variable $variable */
        $variable = $expression->var;
        /** @var \PhpParser\Node\Identifier $varName */
        $varName = $variable->name;
        $key = $valueExtractorStrategyCollection->execute($expression->dim);

        return new ExtractedValueTransfer(sprintf('$%s[%s]', $varName, $key->getValue()), true);
    }
}
