<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\FileNormalizer;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\FileNormalizer\CodeSnifferCompositeNormalizer;
use SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface;
use SprykerSdk\Integrator\Composer\ComposerLockReaderInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use Symfony\Component\Filesystem\Filesystem;

class CodeSnifferCompositeNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsApplicableShouldReturnAlwaysTrue(): void
    {
        // Arrange
        $normalizerOne = $this->createNormalizerMock(false, false);
        $normalizerTwo = $this->createNormalizerMock(true, false);

        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer(
            [$normalizerOne, $normalizerTwo],
            $this->createComposerLockReaderMock(),
            $this->createIntegratorConfigMock(),
            $this->createFilesystemMock(),
        );

        // Act
        $result = $codeSnifferCompositeNormalizer->isApplicable();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testNormalizeShouldCallApplicableNormalizer(): void
    {
        // Arrange & Assert
        $normalizerOne = $this->createNormalizerMock(false, false);
        $normalizerTwo = $this->createNormalizerMock(true, true);

        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer(
            [$normalizerOne, $normalizerTwo],
            $this->createComposerLockReaderMock(),
            $this->createIntegratorConfigMock(),
            $this->createFilesystemMock(),
        );

        // Act
        $codeSnifferCompositeNormalizer->normalize([]);
    }

    /**
     * @return void
     */
    public function testGetErrorMessageShouldReturnErrorMessage(): void
    {
        // Arrange
        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer(
            [$this->createMock(FileNormalizerInterface::class)],
            $this->createComposerLockReaderMock(),
            $this->createIntegratorConfigMock(),
            $this->createFilesystemMock(),
        );

        // Act
        $errorMessage = $codeSnifferCompositeNormalizer->getErrorMessage();

        // Assert
        $this->assertSame(CodeSnifferCompositeNormalizer::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * @dataProvider missedPhpConfigConditionsDataProvider
     *
     * @param bool $isSprykerPackageInstalled
     * @param bool $isCsFixerExecutableFound
     * @param bool $isCsFixerConfigFound
     *
     * @return void
     */
    public function testNormalizeShouldSkipConfigCoppingWhenConditionFalse(
        bool $isSprykerPackageInstalled,
        bool $isCsFixerExecutableFound,
        bool $isCsFixerConfigFound
    ): void
    {
        // Arrange & Assert
        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer(
            [$this->createNormalizerMock(true, true)],
            $this->createComposerLockReaderMock($isSprykerPackageInstalled),
            $this->createIntegratorConfigMock(),
            $this->createFilesystemMock($isCsFixerExecutableFound, $isCsFixerConfigFound),
        );

        // Act
        $codeSnifferCompositeNormalizer->normalize([]);
    }

    /**
     * @return array<array<bool>>
     */
    public function missedPhpConfigConditionsDataProvider(): array
    {
        return [
            [false, true, false],
            [true, false, false],
            [true, true, true],
        ];
    }

    /**
     * @return void
     */
    public function testNormalizeShouldAddAndRemoveMissedPhpcsConfigAndCallApplicableNormalizer(): void
    {
        // Arrange & Assert

        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer(
            [$this->createNormalizerMock(true, true)],
            $this->createComposerLockReaderMock(true),
            $this->createIntegratorConfigMock(),
            $this->createFilesystemMock(true, false, true),
        );

        // Act
        $codeSnifferCompositeNormalizer->normalize([]);
    }

    /**
     * @return void
     */
    public function testNormalizeShouldRemoveMissedPhpcsConfigWhenExceptionThrown(): void
    {
        // Arrange & Assert
        $this->expectException(\InvalidArgumentException::class);

        $fileNormalizersMock = $this->createMock(FileNormalizerInterface::class);
        $fileNormalizersMock->method('isApplicable')->willReturn(true);
        $fileNormalizersMock->method('normalize')->willThrowException(new \InvalidArgumentException(''));


        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer(
            [$fileNormalizersMock],
            $this->createComposerLockReaderMock(true),
            $this->createIntegratorConfigMock(),
            $this->createFilesystemMock(true, false, true),
        );

        // Act
        $codeSnifferCompositeNormalizer->normalize([]);
    }

    /**
     * @param bool $isApplicable
     * @param bool $shouldCall
     * @param string|null $errorMessage
     *
     * @return \SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface
     */
    protected function createNormalizerMock(bool $isApplicable, bool $shouldCall, ?string $errorMessage = null): FileNormalizerInterface
    {
        $fileNormalizersMock = $this->createMock(FileNormalizerInterface::class);
        $fileNormalizersMock->method('isApplicable')->willReturn($isApplicable);
        $fileNormalizersMock->method('getErrorMessage')->willReturn($errorMessage);
        $fileNormalizersMock
            ->expects($shouldCall ? $this->once() : $this->never())
            ->method('normalize');

        return $fileNormalizersMock;
    }

    /**
     * @param bool $isSprykerPackageInstalled
     *
     * @return \SprykerSdk\Integrator\Composer\ComposerLockReaderInterface
     */
    protected function createComposerLockReaderMock(bool $isSprykerPackageInstalled = false): ComposerLockReaderInterface
    {
        $composerLockReader = $this->createMock(ComposerLockReaderInterface::class);
        $composerLockReader->method('getPackageData')->willReturn(
            $isSprykerPackageInstalled ? ['name' => CodeSnifferCompositeNormalizer::SPRYKER_CS_PACKAGE] : null
        );

        return $composerLockReader;
    }

    /**
     * @param bool $isCsFixerExecutableFound
     * @param bool $isCsFixerConfigFound
     * @param bool $shouldInvokeConfigCopping
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function createFilesystemMock(
        bool $isCsFixerExecutableFound = false,
        bool $isCsFixerConfigFound = true,
        bool $shouldInvokeConfigCopping = false
    ): Filesystem
    {
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->method('exists')
            ->willReturnMap([
                [CodeSnifferCompositeNormalizer::PHP_CS_FIXER_PATH, $isCsFixerExecutableFound],
                [CodeSnifferCompositeNormalizer::PHP_CS_FIXER_CONFIG_PATH, $isCsFixerConfigFound],
            ]);

        $filesystem
            ->expects($shouldInvokeConfigCopping ? $this->once() : $this->never())
            ->method('copy')
            ->with(CodeSnifferCompositeNormalizer::getInitialCsFixerConfig());

        $filesystem
            ->expects($shouldInvokeConfigCopping ? $this->once() : $this->never())
            ->method('remove')
            ->with(CodeSnifferCompositeNormalizer::PHP_CS_FIXER_CONFIG_PATH);

        return $filesystem;
    }

    protected function createIntegratorConfigMock(): IntegratorConfig
    {
        $integratorConfig = $this->createMock(IntegratorConfig::class);
        $integratorConfig->method('getProjectRootDirectory')->willReturn('');

        return $integratorConfig;
    }
}
