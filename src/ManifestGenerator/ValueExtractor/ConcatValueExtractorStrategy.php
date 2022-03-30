<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;

class ConcatValueExtractorStrategy implements ValueExtractorStrategyInterface
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
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        $leftPart = $valueExtractorStrategyCollection->execute($expression->left)->getValue();
        $rightPart = $valueExtractorStrategyCollection->execute($expression->right)->getValue();

        return new ExtractedValueObject(
            sprintf('%s . %s', $leftPart, $rightPart),
            true,
        );
    }
}
