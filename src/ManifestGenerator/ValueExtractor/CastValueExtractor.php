<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Cast\Array_;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Node\Expr\Cast\String_;

class CastValueExtractor implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof Bool_
            || $expression instanceof Int_
            || $expression instanceof String_
            || $expression instanceof Array_
            || $expression instanceof Double;
    }

    /**
     * @param \PhpParser\Node\Expr\Cast $expression
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        $value = $valueExtractorStrategyCollection->execute($expression->expr);

        return new ExtractedValueObject(
            sprintf('(%s)%s', $this->generateTypeCastString($expression), $value->getValue()),
            true,
        );
    }

    /**
     * @param \PhpParser\Node\Expr\Cast $castExpression
     *
     * @return string
     */
    protected function generateTypeCastString(Cast $castExpression): string
    {
        if ($castExpression instanceof Double) {
            return 'float';
        }

        $className = get_class($castExpression);
        $classNameParts = explode('\\', $className);

        $classNamePart = end($classNameParts);

        return str_replace('_', '', mb_strtolower($classNamePart));
    }
}
