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

class CodeSnifferCompositeNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsApplicableShouldReturnTrueWhenOneOfNormalizerIsApplicable(): void
    {
        // Arrange
        $normalizerOne = $this->createNormalizerMock(false, false);
        $normalizerTwo = $this->createNormalizerMock(true, false);

        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer([$normalizerOne, $normalizerTwo]);

        // Act
        $result = $codeSnifferCompositeNormalizer->isApplicable();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsApplicableShouldReturnFalseWhenAllNormalizerNotApplicable(): void
    {
        // Arrange
        $normalizerOne = $this->createNormalizerMock(false, false);
        $normalizerTwo = $this->createNormalizerMock(false, false);

        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer([$normalizerOne, $normalizerTwo]);

        // Act
        $result = $codeSnifferCompositeNormalizer->isApplicable();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testIsApplicableShouldNormalizeApplicableNormalizer(): void
    {
        // Arrange & Assert
        $normalizerOne = $this->createNormalizerMock(false, false);
        $normalizerTwo = $this->createNormalizerMock(true, true);

        $codeSnifferCompositeNormalizer = new CodeSnifferCompositeNormalizer([$normalizerOne, $normalizerTwo]);

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
}
