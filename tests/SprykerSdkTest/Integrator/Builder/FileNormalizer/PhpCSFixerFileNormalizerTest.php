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
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerService;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
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
        $processExecutorMock->expects($this->atLeastOnce())->method('run')->with(
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
     * @return void
     */
    public function testGetErrorMessageShouldReturnErrorMessage(): void
    {
        // Arrange
        $processExecutorMock = $this->createProcessExecutorMock(2, 'process error');
        $configMock = $this->createMock(IntegratorConfig::class);
        $normalizer = new PhpCSFixerFileNormalizer($configMock, $processExecutorMock);

        // Act
        $errorMessage = $normalizer->getErrorMessage();

        // Assert
        $this->assertNull($errorMessage);
    }

    /**
     * @param int $exitCode
     * @param string $errorOutput
     *
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected function createProcessExecutorMock(int $exitCode, string $errorOutput = ''): ProcessRunnerServiceInterface
    {
        $processMock = $this->createProcessMock($exitCode, $errorOutput);
        $processExecutorMock = $this->createMock(ProcessRunnerService::class);
        $processExecutorMock->method('run')->willReturn($processMock);

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
