<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Extractor\ValueExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use SprykerSdk\Integrator\Transfer\ExtractedValueTransfer;

class ArrayValueExtractorStrategy extends AbstractValueExtractorStrategy implements ValueExtractorStrategyInterface
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
     * @param \SprykerSdk\Integrator\Builder\Extractor\ValueExtractor\ValueExtractorStrategyCollection $valueExtractorStrategyCollection
     *
     * @return \SprykerSdk\Integrator\Transfer\ExtractedValueTransfer
     */
    public function extractValue(
        Expr $expression,
        ValueExtractorStrategyCollection $valueExtractorStrategyCollection
    ): ExtractedValueTransfer {
        $array = [];
        /** @var \PhpParser\Node\Expr\ArrayItem $item */
        foreach ($expression->items as $key => $item) {
            if (!$item->key) {
                $array[] = $valueExtractorStrategyCollection->execute($item->value)->getValue();

                continue;
            }
            if ($item->key instanceof ClassConstFetch) {
                $key = $valueExtractorStrategyCollection->execute($item->key)->getValue();
            }
            if ($item->key instanceof String_) {
                $key = $item->key->value;
                $array[$key] = $valueExtractorStrategyCollection->execute($item->value)->getValue();

                continue;
            }
            $array[$key] = $valueExtractorStrategyCollection->execute($item->value)->getValue();
        }

        return new ExtractedValueTransfer($array);
    }
}
