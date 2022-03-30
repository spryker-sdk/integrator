<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;

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
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        /** @var \PhpParser\Node\Expr\Variable $variable */
        $variable = $expression->var;
        $varName = $variable->name;
        $key = $valueExtractorStrategyCollection->execute($expression->dim);

        return new ExtractedValueObject(sprintf('$%s[%s]', $varName, $key->getValue()), true);
    }
}
