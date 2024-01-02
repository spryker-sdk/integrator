<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use RuntimeException;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class CodeSnifferCommandExecutor
{
    /**
     * @var int
     */
    public const EXECUTION_ATTEMPTS = 3;

    /**
     * @var int
     */
    protected const SUCCESS_COMMAND_CODE = 0;

    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @param \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param array<string> $command
     *
     * @return void
     */
    public function executeCodeSnifferCommand(array $command): void
    {
        $leftAttempts = static::EXECUTION_ATTEMPTS;

        while ($leftAttempts > 0) {
            $process = $this->processRunner->run($command);

            --$leftAttempts;

            if ($process->getExitCode() === static::SUCCESS_COMMAND_CODE) {
                break;
            }

            if ($leftAttempts === 0 && $process->getExitCode() !== static::SUCCESS_COMMAND_CODE) {
                $this->throwFailedExecutionException($process);
            }
        }
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function throwFailedExecutionException(Process $process): void
    {
        $errorOutput = $process->getErrorOutput();

        if ($errorOutput !== '') {
            throw new RuntimeException($errorOutput);
        }
    }
}
