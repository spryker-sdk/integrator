<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\DNumber;

class FloatValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof DNumber;
    }

    /**
     * @param \PhpParser\Node\Scalar\DNumber $expression
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        return new ExtractedValueObject((float)$expression->value);
    }
}
