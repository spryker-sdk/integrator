<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use RuntimeException;
use SprykerSdk\Integrator\Executor\ProcessExecutorInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class CodeSniffStyleFileNormalizer implements FileNormalizerInterface
{
    /**
     * @var string
     */
    protected const PROJECT_CONSOLE_PATH = 'vendor/bin/console';

    /**
     * @var string
     */
    protected const PHP_CS_FIX_COMMAND = 'code:sniff:style -f';

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @var \SprykerSdk\Integrator\Executor\ProcessExecutorInterface
     */
    protected ProcessExecutorInterface $processExecutor;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \SprykerSdk\Integrator\Executor\ProcessExecutorInterface $processExecutor
     */
    public function __construct(IntegratorConfig $config, ProcessExecutorInterface $processExecutor)
    {
        $this->config = $config;
        $this->processExecutor = $processExecutor;
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return !is_file($this->config->getPhpCsConfigPath()) && is_file($this->getProjectConsolePath());
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
     * @throws \RuntimeException
     *
     * @return void
     */
    public function normalize(array $filePaths): void
    {
        $projectConsolePath = $this->getProjectConsolePath();
        foreach ($this->getProjectRelativeFilePaths($filePaths) as $filePath) {
            $process = $this->processExecutor->execute([$projectConsolePath, static::PHP_CS_FIX_COMMAND, $filePath]);

            if ($process->getExitCode() > 0 && $process->getErrorOutput() !== '') {
                throw new RuntimeException($process->getErrorOutput());
            }
        }
    }

    /**
     * @param array $filePaths
     *
     * @return array
     */
    protected function getProjectRelativeFilePaths(array $filePaths): array
    {
        $projectDir = $this->config->getProjectRootDirectory();
        if (!$projectDir) {
            return $filePaths;
        }

        return array_map(
            static fn (string $filepath): string => strpos($filepath, $projectDir) === 0
                ? str_replace($projectDir, ' ', $filepath)
                : $filepath,
            $filePaths,
        );
    }

    /**
     * @return string
     */
    protected function getProjectConsolePath(): string
    {
        return $this->config->getProjectRootDirectory() . static::PROJECT_CONSOLE_PATH;
    }
}
