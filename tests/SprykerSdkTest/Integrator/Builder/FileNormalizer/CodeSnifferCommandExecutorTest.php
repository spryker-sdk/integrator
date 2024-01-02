<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\FileNormalizer;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use SprykerSdk\Integrator\Builder\FileNormalizer\CodeSnifferCommandExecutor;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class CodeSnifferCommandExecutorTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteCodeSnifferCommandShouldSuccessfulRun(): void
    {
        // Arrange
        $executionCount = 1;
        $codeSnifferCommandExecutor = new CodeSnifferCommandExecutor(
            $this->createProcessRunnerServiceMock($executionCount, $this->createProcessMock(0, '')),
        );

        // Act
        $codeSnifferCommandExecutor->executeCodeSnifferCommand([]);
    }

    /**
     * @return void
     */
    public function testExecuteCodeSnifferCommandShouldSilentlyFailWhenNoErrorOutput(): void
    {
        // Arrange
        $codeSnifferCommandExecutor = new CodeSnifferCommandExecutor(
            $this->createProcessRunnerServiceMock(CodeSnifferCommandExecutor::EXECUTION_ATTEMPTS, $this->createProcessMock(1, '')),
        );

        // Act
        $codeSnifferCommandExecutor->executeCodeSnifferCommand([]);
    }

    /**
     * @return void
     */
    public function testExecuteCodeSnifferCommandShouldFailWhenErrorOutputNotEmpty(): void
    {
        // Arrange
        $exceptionMessage = 'Some error';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $codeSnifferCommandExecutor = new CodeSnifferCommandExecutor(
            $this->createProcessRunnerServiceMock(CodeSnifferCommandExecutor::EXECUTION_ATTEMPTS, $this->createProcessMock(1, $exceptionMessage)),
        );

        // Act
        $codeSnifferCommandExecutor->executeCodeSnifferCommand([]);
    }

    /**
     * @param int $expectedExecutionCount
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected function createProcessRunnerServiceMock(int $expectedExecutionCount, Process $process): ProcessRunnerServiceInterface
    {
        $processRunnerService = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerService
            ->expects($this->exactly($expectedExecutionCount))
            ->method('run')
            ->willReturn($process);

        return $processRunnerService;
    }

    /**
     * @param int $exitCode
     * @param string $errorOutput
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function createProcessMock(int $exitCode, string $errorOutput): Process
    {
        $process = $this->createMock(Process::class);
        $process->method('getExitCode')->willReturn($exitCode);
        $process->method('getErrorOutput')->willReturn($errorOutput);

        return $process;
    }
}
