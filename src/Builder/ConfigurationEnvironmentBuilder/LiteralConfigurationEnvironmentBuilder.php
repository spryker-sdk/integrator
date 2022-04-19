<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder;

use SprykerSdk\Integrator\IntegratorConfig;

class LiteralConfigurationEnvironmentBuilder implements ConfigurationEnvironmentBuilderInterface
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isApplicable($value): bool
    {
        if (!is_array($value) || empty($value[IntegratorConfig::MANIFEST_KEY_IS_LITERAL])) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getFormattedExpression($value): string
    {
        return $value[IntegratorConfig::MANIFEST_KEY_VALUE];
    }
}
