<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class AssignValueExtractor extends AbstractValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof Assign;
    }

    /**
     * @param \PhpParser\Node\Expr\Assign $expression
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(
        Expr $expression,
        ValueExtractorStrategyCollection $valueExtractorStrategyCollection
    ): ExtractedValueTransfer {
        /** @var \PhpParser\Node\Expr\Variable $expressionVar */
        $expressionVar = $expression->var;

        if (!property_exists($expressionVar, 'name') && property_exists($expressionVar, 'var')) {
            $expressionVar = $expressionVar->var;
        }

        $extractedValue = sprintf(
            '$%s = %s;',
            property_exists($expressionVar, 'name') ? $expressionVar->name : '_unknown_',
            $this->getQuotedValueExpression($expression->expr, $valueExtractorStrategyCollection),
        );

        return new ExtractedValueTransfer($extractedValue, true);
    }
}
