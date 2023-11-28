<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Ternary;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class TernaryValueExtractorStrategy extends AbstractValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof Ternary;
    }

    /**
     * @param \PhpParser\Node\Expr\Ternary $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueTransfer
    {
        $conditionExpression = $this->getQuotedValueExpression($expression->cond, $valueExtractorStrategyCollection);
        $elseStatementExpression = $this->getQuotedValueExpression($expression->else, $valueExtractorStrategyCollection);

        if ($expression->if) {
            $ifStatementExpression = $this->getQuotedValueExpression($expression->if, $valueExtractorStrategyCollection);

            return new ExtractedValueTransfer(
                sprintf('%s ? %s : %s', $conditionExpression, $ifStatementExpression, $elseStatementExpression),
                true,
            );
        }

        return new ExtractedValueTransfer(
            sprintf('%s ?: %s', $conditionExpression, $elseStatementExpression),
            true,
        );
    }
}
