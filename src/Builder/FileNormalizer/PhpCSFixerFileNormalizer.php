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
        return is_file($this->config->getPhpCsConfigPath()) && is_file($this->getCSFixPath());
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
        $process = $this->processExecutor->execute([$this->getCSFixPath(), ...$this->getAbsoluteFilePaths($filePaths)]);
        if ($process->getExitCode() > 0 && $process->getErrorOutput() !== '') {
            throw new RuntimeException($process->getErrorOutput());
        }
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
        return $this->config->getProjectRootDirectory() . static::PHP_CS_FIX_RELATIVE_PATH;
    }
}
