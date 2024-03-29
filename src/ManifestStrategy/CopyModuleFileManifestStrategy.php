<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Exception\ManifestApplyingException;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Utils\Infrastructure\Helper\StrHelper;

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
     * @param array<mixed> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @throws \SprykerSdk\Integrator\Exception\ManifestApplyingException
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        $source = $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE];
        $sourcePath = $this->getSourcePath($source, $moduleName);
        $targetPath = $this->getTargetPath($manifest);
        $targetDir = dirname($targetPath);

        if (!file_exists($sourcePath)) {
            throw new ManifestApplyingException(sprintf('Source file not found `%s`', $sourcePath));
        }

        if (file_exists($targetPath)) {
            throw new ManifestApplyingException(sprintf('Target file exists `%s`', $targetPath));
        }

        if (!$isDry && !is_dir($targetDir)) {
            mkdir($targetDir, 0770, true);
        }

        if (!$isDry && !copy($sourcePath, $targetPath)) {
            throw new ManifestApplyingException(sprintf(
                'Error during file coping, source `%s`, target `%s`',
                $sourcePath,
                $targetPath,
            ));
        }

        $inputOutput->writeln(sprintf(
            'File %s was copied to %s',
            $source,
            $targetPath,
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

        return $this->config->getVendorDirectory()
            . mb_strtolower(StrHelper::camelCaseToDash($organisation))
            . DIRECTORY_SEPARATOR
            . mb_strtolower(StrHelper::camelCaseToDash($moduleName))
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
}
