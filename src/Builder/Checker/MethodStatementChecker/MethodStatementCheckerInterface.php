<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker;

interface MethodStatementCheckerInterface
{
    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    public function isApplicable($previousValue, $currentValue): bool;

    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    public function isSameStatement($previousValue, $currentValue): bool;
}
