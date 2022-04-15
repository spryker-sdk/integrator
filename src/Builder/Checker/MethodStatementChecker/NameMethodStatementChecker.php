<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker;

use SprykerSdk\Integrator\Builder\Checker\ClassMethodChecker;

class NameMethodStatementChecker extends AbstractMethodStatementChecker implements MethodStatementCheckerInterface
{
    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    public function isApplicable($previousValue, $currentValue): bool
    {
        return $this->isExistsStatementsField($previousValue, $currentValue, ClassMethodChecker::METHOD_FIELD_NAME);
    }

    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    public function isSameStatements($previousValue, $currentValue): bool
    {
        if (is_string($previousValue->name) && is_string($currentValue->name) && $previousValue->name !== $currentValue->name) {
            return false;
        }
        if (
            $this->isExistsStatementsField($previousValue->name, $currentValue->name, 'parts')
            && $previousValue->name->parts !== $currentValue->name->parts
        ) {
            return false;
        }
        if (property_exists($previousValue->name, 'parts') && !property_exists($currentValue->name, 'parts')) {
            return false;
        }
        if (!property_exists($previousValue->name, 'parts') && property_exists($currentValue->name, 'parts')) {
            return false;
        }

        return true;
    }
}
