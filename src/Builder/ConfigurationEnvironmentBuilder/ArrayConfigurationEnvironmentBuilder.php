<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder;

use SprykerSdk\Integrator\IntegratorConfig;

class ArrayConfigurationEnvironmentBuilder implements ConfigurationEnvironmentBuilderInterface
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isApplicable($value): bool
    {
        if (!is_array($value) || !empty($value[IntegratorConfig::MANIFEST_KEY_IS_LITERAL])) {
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
        $result = ['['];
        foreach ($value as $keyValueItem => $valueItem) {
            $outputFormat = '\'%s\',';
            if (is_array($valueItem)) {
                $valueItem = $this->getFormattedExpression($valueItem);
                $outputFormat = '%s,';
            }
            if (is_int($keyValueItem)) {
                $result[] = sprintf($outputFormat, trim($valueItem, '\''));

                continue;
            }
            $formattedKeyValue = sprintf('\'%s\'', trim($keyValueItem, '\''));
            $formattedValue = sprintf('\'%s\'', trim($valueItem, '\''));
            $result[] = sprintf('%s => %s,', $formattedKeyValue, $formattedValue);
        }
        $result[] = ']';

        return implode("\n", $result);
    }
}
