<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder;

interface ConfigurationEnvironmentStrategyInterface
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isApplicable($value): bool;

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getFormattedExpression($value): string;
}
