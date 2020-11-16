<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business\ManifestStrategy;

use SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface;
use SprykerSdk\Zed\Integrator\IntegratorConfig;

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
     * @param string[] $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, IOInterface $inputOutput, bool $isDry): bool
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
                $defaultValue
            );
        }

        $configFileName = $this->config->getSharedConfigPath();
        if (!file_exists($configFileName)) {
            return false;
        }
        if (!$isDry) {
            file_put_contents($configFileName, $this->getConfigAppendData($target, $value), FILE_APPEND);
        }

        $inputOutput->writeln(sprintf(
            'Global config %s was added with to %s value %s',
            $target,
            $configFileName,
            $value
        ), IOInterface::DEBUG);

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
        $data = PHP_EOL . $this->config->getSharedConfigVariableName() . '[' . $target . '] = ';
        $data .= var_export($value, true);
        $data .= ';' . PHP_EOL;

        return $data;
    }
}
