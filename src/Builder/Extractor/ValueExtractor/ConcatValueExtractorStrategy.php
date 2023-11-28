<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class ConcatValueExtractorStrategy extends AbstractValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof Concat;
    }

    /**
     * @param \PhpParser\Node\Expr\BinaryOp\Concat $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueTransfer
    {
        $leftPart = $this->getQuotedValueExpression($expression->left, $valueExtractorStrategyCollection);
        $rightPart = $this->getQuotedValueExpression($expression->right, $valueExtractorStrategyCollection);

        return new ExtractedValueTransfer(
            sprintf('%s . %s', $leftPart, $rightPart),
            true,
        );
    }
}
