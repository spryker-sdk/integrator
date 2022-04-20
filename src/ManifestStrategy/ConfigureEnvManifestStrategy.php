<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class ConfigureEnvManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'configure-env';
    }

    /**
     * @param array<mixed> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        $target = $manifest[IntegratorConfig::MANIFEST_KEY_TARGET];
        $value = $manifest[IntegratorConfig::MANIFEST_KEY_VALUE] ?? null;
        $choices = $manifest[IntegratorConfig::MANIFEST_KEY_CHOICES] ?? [];
        $defaultValue = $manifest[IntegratorConfig::MANIFEST_KEY_DEFAULT_VALUE] ?? null;

        if (!$value) {
            $value = $this->askValue(
                "'Provide value for $target global configuration.'",
                $choices,
                $inputOutput,
                $defaultValue,
            );
        }

        $configFileName = $this->config->getConfigPath();
        if (!file_exists($configFileName)) {
            $inputOutput->writeln(sprintf(
                'File %s does not exist. Please check filepath.',
                $configFileName,
            ), InputOutputInterface::DEBUG);

            return false;
        }
        if (!$isDry) {
            file_put_contents($configFileName, $this->getConfigAppendData($target, $value), FILE_APPEND);
        }

        $inputOutput->writeln(sprintf(
            'Global config %s was added with to %s value %s',
            $target,
            $configFileName,
            $value,
        ), InputOutputInterface::DEBUG);

        return true;
    }

    /**
     * @param string $target
     * @param mixed $value
     *
     * @return string
     */
    protected function getConfigAppendData(string $target, $value): string
    {
        $data = PHP_EOL . '$' . $this->config->getConfigVariableName() . '[' . $target . '] = ';

        $data .= $this->prepareValue($value);
        $data .= ';' . PHP_EOL;

        return $data;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function prepareValue($value)
    {
        if (is_array($value) && isset($value['is_literal'])) {
            return $value['value'];
        }

        return var_export($value, true);
    }
}
