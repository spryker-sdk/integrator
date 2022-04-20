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
        return $this->createExpressionStringFromValue($value);
    }

    /**
     * @param mixed $value
     * @param int $level
     *
     * @return string
     */
    protected function createExpressionStringFromValue($value, int $level = 0): string
    {
        $level++;
        $result = ['['];
        foreach ($value as $keyValueItem => $valueItem) {
            $outputFormat = '\'%s\',';
            if (is_array($valueItem)) {
                $valueItem = $this->createExpressionStringFromValue($valueItem, $level);
                $outputFormat = '%s,';
            }
            if (is_int($keyValueItem)) {
                $result[] = $this->createIndent($level) . sprintf($outputFormat, trim($valueItem, '\''));

                continue;
            }
            $formattedKeyValue = $this->getFormattedValueExpression($keyValueItem);
            $formattedValue = $this->getFormattedValueExpression($valueItem);
            $result[] = $this->createIndent($level) . sprintf('%s => %s,', $formattedKeyValue, $formattedValue);
        }
        $result[] = $this->createIndent($level - 1) . ']';

        return implode("\n", $result);
    }

    /**
     * @param int $level
     *
     * @return string
     */
    protected function createIndent(int $level): string
    {
        return str_repeat(' ', ($level) * 4);
    }

    /**
     * @param string $expression
     *
     * @return bool
     */
    protected function isComplicatedExpression(string $expression): bool
    {
        return (bool)preg_match('/[\(\)\?]/', $expression);
    }

    /**
     * @param string $expression
     *
     * @return string
     */
    protected function getFormattedValueExpression(string $expression): string
    {
        if ($this->isComplicatedExpression($expression)) {
            return $expression;
        }

        return sprintf('\'%s\'', trim($expression, '\''));
    }
}
