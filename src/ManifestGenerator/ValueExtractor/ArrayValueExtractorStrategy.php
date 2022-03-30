<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;

class ArrayValueExtractorStrategy implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof Array_;
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $expression
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        $array = [];

        foreach ($expression->items as $key => $item) {
            $key = $item->key instanceof String_ ? $item->key->value : $key;
            $array[$key] = $valueExtractorStrategyCollection->execute($item->value)->getValue();
        }

        return new ExtractedValueObject($array);
    }
}
