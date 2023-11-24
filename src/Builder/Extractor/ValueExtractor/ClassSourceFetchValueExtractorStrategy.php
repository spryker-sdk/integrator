<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\New_;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class ClassSourceFetchValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof New_;
    }

    /**
     * @param \PhpParser\Node\Expr\New_ $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(
        Expr $expression,
        ValueExtractorStrategyCollection $valueExtractorStrategyCollection
    ): ExtractedValueTransfer {
        $prefix = strpos($expression->class->toString(), '\\') ? '\\' : '';

        return new ExtractedValueTransfer(
            sprintf('%s%s', $prefix, $expression->class->toString()),
            false,
            true,
        );
    }
}
