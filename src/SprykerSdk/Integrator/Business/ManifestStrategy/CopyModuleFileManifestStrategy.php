<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Business\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use Zend\Filter\Word\CamelCaseToSeparator;

class CopyModuleFileManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'copy-module-file';
    }

    /**
     * @param string[] $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        $source = $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE];
        $sourcePath = $this->getSourcePath($source, $moduleName);
        $targetPath = $this->getTargetPath($manifest);

        if (!file_exists($sourcePath) || file_exists($targetPath)) {
            return false;
        }

        if (!$isDry && !copy($sourcePath, $targetPath)) {
            return false;
        }

        $inputOutput->writeln(sprintf(
            'File %s was copied to %s',
            $source,
            $targetPath
        ), InputOutputInterface::DEBUG);

        return true;
    }

    /**
     * @param string $source
     * @param string $moduleName
     *
     * @return string
     */
    protected function getSourcePath(string $source, string $moduleName): string
    {
        [$organisation, $moduleName] = explode('.', $moduleName);

        return $this->config->getCoreRootDirectory()
            . strtolower($this->camelCaseToDash($organisation))
            . DIRECTORY_SEPARATOR
            . strtolower($this->camelCaseToDash($moduleName))
            . DIRECTORY_SEPARATOR
            . $source;
    }

    /**
     * @param array $manifest
     *
     * @return string
     */
    protected function getTargetPath(array $manifest): string
    {
        $target = $manifest[IntegratorConfig::MANIFEST_KEY_TARGET];

        return $this->config->getProjectRootDirectory() . $target;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function camelCaseToDash(string $value): string
    {
        return (new CamelCaseToSeparator('-'))->filter($value);
    }
}
