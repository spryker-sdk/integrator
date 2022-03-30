<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;

class FuncCallValueExtractor implements ValueExtractorStrategyInterface
{
    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @return bool
     */
    public function isApplicable(Expr $expression): bool
    {
        return $expression instanceof FuncCall;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $expression
     * @param \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function extractValue(Expr $expression, ValueExtractorStrategyCollection $valueExtractorStrategyCollection): ExtractedValueObject
    {
        $funcName = $expression->name->toString();

        $args = [];
        foreach ($expression->args as $arg) {
            $args[] = var_export($valueExtractorStrategyCollection->execute($arg->value)->getValue(), true);
        }

        return new ExtractedValueObject(sprintf('%s(%s)', $funcName, implode(', ', $args)), true);
    }
}
