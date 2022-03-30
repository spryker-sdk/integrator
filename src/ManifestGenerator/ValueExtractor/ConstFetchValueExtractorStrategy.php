<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;

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
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        return new ExtractedValueObject($this->filterValue($expression->name->toString()));
    }

    /**
     * @param string $value
     *
     * @return string|bool
     */
    protected function filterValue(string $value)
    {
        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        return $value;
    }
}
