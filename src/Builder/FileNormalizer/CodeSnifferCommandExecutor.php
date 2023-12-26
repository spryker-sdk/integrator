<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\FileNormalizer;

use RuntimeException;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;

class CodeSnifferCommandExecutor
{
    /**
     * @var int<1, max>
     */
    public const EXECUTION_ATTEMPTS = 2;

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
     * @throws \RuntimeException
     *
     * @return void
     */
    public function executeCodeSnifferCommand(array $command): void
    {
        $leftAttempts = static::EXECUTION_ATTEMPTS;

        while ($leftAttempts > 0) {
            $process = $this->processRunner->run($command);

            if ($process->getExitCode() === static::SUCCESS_COMMAND_CODE) {
                break;
            }

            --$leftAttempts;
        }

        if ($process->getExitCode() !== static::SUCCESS_COMMAND_CODE) {
            $errorOutput = $process->getErrorOutput();

            if ($errorOutput !== '') {
                throw new RuntimeException($errorOutput);
            }
        }
    }
}
