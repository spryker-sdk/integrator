<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\FileNormalizer;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use SprykerSdk\Integrator\Builder\FileNormalizer\PhpCSFixerFileNormalizer;
use SprykerSdk\Integrator\Builder\FileStorage\FileStorage;
use SprykerSdk\Integrator\Executor\ProcessExecutor;
use SprykerSdk\Integrator\Executor\ProcessExecutorInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use Symfony\Component\Process\Process;

class PhpCSFixerFileNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteSuccess(): void
    {
        // Arrange
        $processExecutorMock = $this->createProcessExecutorMock(0, '');
        $processExecutorMock->expects($this->once())->method('execute')->with(
            $this->callback(function ($command) {
                return strpos($command[0], 'vendor/bin/phpcbf') !== false;
            }),
        );

        // Arrange
        $configMock = $this->createMock(IntegratorConfig::class);
        $normalizer = new PhpCSFixerFileNormalizer($configMock, $processExecutorMock);

        $fileStorage = new FileStorage();
        $fileStorage->addFile('someClass.php');

        // Act
        $normalizer->normalize($fileStorage->flush());
    }

    /**
     * @return void
     */
    public function testExecuteFailed(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $processExecutorMock = $this->createProcessExecutorMock(2, 'process error');
        $configMock = $this->createMock(IntegratorConfig::class);
        $normalizer = new PhpCSFixerFileNormalizer($configMock, $processExecutorMock);

        $fileStorage = new FileStorage();
        $fileStorage->addFile('someClass.php');

        // Act
        $normalizer->normalize($fileStorage->flush());
    }

    /**
     * @param int $exitCode
     * @param string $errorOutput
     *
     * @return \SprykerSdk\Integrator\Executor\ProcessExecutorInterface
     */
    protected function createProcessExecutorMock(int $exitCode, string $errorOutput = ''): ProcessExecutorInterface
    {
        $processMock = $this->createProcessMock($exitCode, $errorOutput);
        $processExecutorMock = $this->createMock(ProcessExecutor::class);
        $processExecutorMock->method('execute')->willReturn($processMock);

        return $processExecutorMock;
    }

    /**
     * @param int $exitCode
     * @param string $errorOutput
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function createProcessMock(int $exitCode, string $errorOutput = ''): Process
    {
        $processMock = $this->createMock(Process::class);
        $processMock->method('getExitCode')->willReturn($exitCode);
        $processMock->method('getErrorOutput')->willReturn($errorOutput);

        return $processMock;
    }
}