<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker;

use SprykerSdk\Integrator\Builder\Checker\ClassMethodChecker;

class PartsMethodStatementChecker extends AbstractMethodStatementChecker implements MethodStatementCheckerInterface
{
    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    public function isApplicable($previousValue, $currentValue): bool
    {
        return $this->isExistsStatementsField($previousValue, $currentValue, ClassMethodChecker::METHOD_FIELD_PARTS);
    }

    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    public function isSameStatements($previousValue, $currentValue): bool
    {
        return $this->isSameStatementsArrayPart($currentValue->parts, $previousValue->parts);
    }
}
