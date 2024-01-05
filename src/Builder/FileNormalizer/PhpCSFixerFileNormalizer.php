<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use SprykerSdk\Integrator\IntegratorConfig;

class PhpCSFixerFileNormalizer implements FileNormalizerInterface
{
    /**
     * @var string
     */
    protected const PHP_CS_FIX_RELATIVE_PATH = 'vendor/bin/phpcbf';

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @var \SprykerSdk\Integrator\Builder\FileNormalizer\CodeSnifferCommandExecutor
     */
    protected CodeSnifferCommandExecutor $codeSnifferCommandExecutor;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \SprykerSdk\Integrator\Builder\FileNormalizer\CodeSnifferCommandExecutor $codeSnifferCommandExecutor
     */
    public function __construct(IntegratorConfig $config, CodeSnifferCommandExecutor $codeSnifferCommandExecutor)
    {
        $this->config = $config;
        $this->codeSnifferCommandExecutor = $codeSnifferCommandExecutor;
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return is_file($this->config->getPhpCsConfigPath());
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return null;
    }

    /**
     * @param array $filePaths
     *
     * @return void
     */
    public function normalize(array $filePaths): void
    {
        $this->codeSnifferCommandExecutor->executeCodeSnifferCommand(
            [$this->getCSFixPath(), ...$this->getAbsoluteFilePaths($filePaths)],
        );
    }

    /**
     * @param array $filePaths
     *
     * @return array
     */
    protected function getAbsoluteFilePaths(array $filePaths): array
    {
        $projectDir = $this->config->getProjectRootDirectory();
        if (!$projectDir) {
            return $filePaths;
        }

        return array_map(
            static fn (string $filepath): string => strpos($filepath, $projectDir) === 0
                ? $filepath
                : $projectDir . DIRECTORY_SEPARATOR . ltrim($filepath, DIRECTORY_SEPARATOR),
            $filePaths,
        );
    }

    /**
     * @return string
     */
    protected function getCSFixPath(): string
    {
        return is_file($this->getProjectCSFixPath()) ? $this->getProjectCSFixPath() : $this->getIntegratorCSFixPath();
    }

    /**
     * @return string
     */
    protected function getProjectCSFixPath(): string
    {
        return $this->config->getProjectRootDirectory() . static::PHP_CS_FIX_RELATIVE_PATH;
    }

    /**
     * @return string
     */
    protected function getIntegratorCSFixPath(): string
    {
        return INTEGRATOR_ROOT_DIR . DIRECTORY_SEPARATOR . static::PHP_CS_FIX_RELATIVE_PATH;
    }
}
