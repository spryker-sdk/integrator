<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use SprykerSdk\Integrator\Builder\Exception\ValueExtractorException;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

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
        TernaryValueExtractorStrategy::class,
        MethodCallExtractorStrategy::class,
        VariableExtractorStrategy::class,
        DefaultIsLiteralExtractorStrategy::class,
    ];

    /**
     * @var array<\SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyInterface>
     */
    protected $strategiesCache = [];

    /**
     * @param \PhpParser\Node\Expr $expression
     *
     * @throws \SprykerSdk\Integrator\Builder\Exception\ValueExtractorException
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function execute(Expr $expression): ExtractedValueTransfer
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
     * @return array<\SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyInterface>
     */
    protected function getStrategies(): array
    {
        if ($this->strategiesCache) {
            return $this->strategiesCache;
        }

        foreach (static::STRATEGIES as $strategyClass) {
            /** @var \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyInterface $strategy */
            $strategy = new $strategyClass();
            $this->strategiesCache[] = $strategy;
        }

        return $this->strategiesCache;
    }
}
