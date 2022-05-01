<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker;

use SprykerSdk\Integrator\Builder\Checker\AbstractMethodChecker;
use SprykerSdk\Integrator\Builder\Checker\ClassMethodChecker;

class ItemsMethodStatementChecker extends AbstractMethodChecker implements MethodStatementCheckerInterface
{
    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    public function isApplicable($previousValue, $currentValue): bool
    {
        return $this->isExistsStatementField($previousValue, $currentValue, ClassMethodChecker::METHOD_FIELD_ITEMS);
    }

    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    public function isSameStatement($previousValue, $currentValue): bool
    {
        return count($previousValue->items) === count($currentValue->items);
    }
}
