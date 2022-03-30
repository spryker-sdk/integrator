<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;

class ClassConstFetchValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof ClassConstFetch;
    }

    /**
     * @param \PhpParser\Node\Expr\ClassConstFetch $expression
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        $prefix = strpos($expression->class->toString(), '\\') ? '\\' : '';

        return new ExtractedValueObject(
            sprintf('%s%s::%s', $prefix, $expression->class->toString(), $expression->name->toString()),
        );
    }
}
