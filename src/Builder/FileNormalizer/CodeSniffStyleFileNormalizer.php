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
     * @var int
     */
    protected const PROCESS_TIMEOUT = 300;

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
     * @return bool
     */
    public function isApplicable(): bool
    {
        return !is_file($this->config->getPhpCSConfigPath()) && is_file($this->getProjectConsolePath());
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
            $process = new Process([$projectConsolePath, static::PHP_CS_FIX_COMMAND, $filePath]);
            $process->setTimeout(static::PROCESS_TIMEOUT);
            $process->run();

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
