<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Checker;

class AbstractMethodChecker
{
    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     * @param string $field
     *
     * @return bool
     */
    protected function isExistsStatementField($previousValue, $currentValue, string $field): bool
    {
        return is_object($previousValue)
            && is_object($currentValue)
            && property_exists($previousValue, $field) && property_exists($currentValue, $field);
    }

    /**
     * @param mixed $previousArray
     * @param mixed $currentArray
     *
     * @return bool
     */
    protected function isSameStatementsArrayPart($previousArray, $currentArray): bool
    {
        return is_array($previousArray) && is_array($currentArray) && $previousArray === $currentArray;
    }
}
