<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\FileNormalizer;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\FileNormalizer\CodeSnifferCommandExecutor;
use SprykerSdk\Integrator\Builder\FileNormalizer\PhpCSFixerFileNormalizer;
use SprykerSdk\Integrator\Builder\FileStorage\FileStorage;
use SprykerSdk\Integrator\IntegratorConfig;

class PhpCSFixerFileNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteSuccess(): void
    {
        // Arrange
        $codesnifferCommandExecutor = $this->createCodeSnifferCommandExecutorMock();
        // Arrange
        $configMock = $this->createMock(IntegratorConfig::class);
        $normalizer = new PhpCSFixerFileNormalizer($configMock, $codesnifferCommandExecutor);

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
        $codesnifferCommandExecutor = $this->createMock(CodeSnifferCommandExecutor::class);
        $configMock = $this->createMock(IntegratorConfig::class);
        $normalizer = new PhpCSFixerFileNormalizer($configMock, $codesnifferCommandExecutor);

        // Act
        $errorMessage = $normalizer->getErrorMessage();

        // Assert
        $this->assertNull($errorMessage);
    }

    /**
     * @return \SprykerSdk\Integrator\Builder\FileNormalizer\CodeSnifferCommandExecutor
     */
    protected function createCodeSnifferCommandExecutorMock(): CodeSnifferCommandExecutor
    {
        $codeSnifferCommandExecutor = $this->createMock(CodeSnifferCommandExecutor::class);
        $codeSnifferCommandExecutor->expects($this->once())
            ->method('executeCodeSnifferCommand')
            ->with(
                $this->callback(function ($command) {
                    return strpos($command[0], 'vendor/bin/phpcbf') !== false;
                }),
            );

        return $codeSnifferCommandExecutor;
    }
}
