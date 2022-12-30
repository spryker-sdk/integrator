<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use RuntimeException;
use SprykerSdk\Integrator\IntegratorConfig;
use Symfony\Component\Process\Process;

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
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     */
    public function __construct(IntegratorConfig $config)
    {
        $this->config = $config;
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
        $process = new Process([$this->getCSFixPath(), ...$this->getAbsoluteFilePaths($filePaths)]);
        $process->run();

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

        return array_map(
            static fn (string $filepath): string => strpos($filepath, $projectDir) === 0
                ? $filepath
                : $projectDir . DIRECTORY_SEPARATOR . ltrim($filepath, DIRECTORY_SEPARATOR),
            $filePaths,
        );
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return is_file($this->getCSFixPath());
    }

    /**
     * @return string
     */
    protected function getCSFixPath(): string
    {
        return $this->config->getProjectRootDirectory() . static::PHP_CS_FIX_RELATIVE_PATH;
    }
}
