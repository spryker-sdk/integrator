<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;

class StringValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof String_;
    }

    /**
     * @param \PhpParser\Node\Scalar\String_ $expression
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        return new ExtractedValueObject((string)$expression->value);
    }
}
