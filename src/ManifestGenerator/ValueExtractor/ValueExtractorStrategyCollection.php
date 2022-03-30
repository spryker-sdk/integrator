<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

use SprykerSdk\Integrator\ManifestGenerator\Exception\ValueExtractorException;
use PhpParser\Node\Expr;

class ValueExtractorStrategyCollection
{
    /**
     * @var array<string>
     */
    protected const STRATEGIES = [
        ClassConstFetchValueExtractorStrategy::class,
        ConstFetchValueExtractorStrategy::class,
        FloatValueExtractorStrategy::class,
        IntegerValueExtractorStrategy::class,
        StringValueExtractorStrategy::class,
        ConcatValueExtractorStrategy::class,
        ArrayValueExtractorStrategy::class,
        CastValueExtractor::class,
        ArrayDimValueExtractor::class,
        FuncCallValueExtractor::class,
    ];

    /**
     * @var array<\SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyInterface>
     */
    protected $strategiesCache = [];

    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @throws \SprykerSdk\Integrator\ManifestGenerator\Exception\ValueExtractorException
     *
     * @return \SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ExtractedValueObject
     */
    public function execute(Expr $expression): ExtractedValueObject
    {
        $strategies = $this->getStrategies();
        foreach ($strategies as $strategy) {
            if ($strategy->isApplicable($expression)) {
                return $strategy->extractValue($expression, $this);
            }
        }

        throw new ValueExtractorException(sprintf('%s expression type is not supported', get_class($expression)));
    }

    /**
     * @return array<\SprykerSdk\Integrator\ManifestGenerator\ValueExtractor\ValueExtractorStrategyInterface>
     */
    protected function getStrategies(): array
    {
        if ($this->strategiesCache) {
            return $this->strategiesCache;
        }

        foreach (static::STRATEGIES as $strategy) {
            $this->strategiesCache[] = new $strategy();
        }

        return $this->strategiesCache;
    }
}
