<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\FileNormalizer;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizerInterface;
use SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizersExecutor;
use SprykerSdk\Integrator\Builder\FileStorage\FileStorage;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;

class FileNormalizersExecutorTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteShouldNotCallNormalizerWhenFileStorageIsEmpty(): void
    {
        //Arrange & Assert
        $fileNormalizerMock = $this->createNormalizerMock(true, false);
        $fileStorage = new FileStorage();
        $fileNormalizersExecutor = new FileNormalizersExecutor($fileStorage, [$fileNormalizerMock]);
        $inputOutput = $this->createMock(InputOutputInterface::class);

        //Act
        $fileNormalizersExecutor->execute($inputOutput, false);
    }

    /**
     * @return void
     */
    public function testExecuteShouldNotCallNormalizerWhenIsNotApplicable(): void
    {
        //Arrange & Assert
        $fileNormalizerMock = $this->createNormalizerMock(false, false);
        $fileStorage = new FileStorage();
        $fileStorage->addFile('someClass.php');
        $fileNormalizersExecutor = new FileNormalizersExecutor($fileStorage, [$fileNormalizerMock]);
        $inputOutput = $this->createMock(InputOutputInterface::class);

        //Act
        $fileNormalizersExecutor->execute($inputOutput, true);
    }

    /**
     * @return void
     */
    public function testExecuteShouldNotCallNormalizerWhenIsDryRunMode(): void
    {
        //Arrange & Assert
        $fileNormalizerMock = $this->createNormalizerMock(true, false);
        $fileStorage = new FileStorage();
        $fileStorage->addFile('someClass.php');
        $fileNormalizersExecutor = new FileNormalizersExecutor($fileStorage, [$fileNormalizerMock]);
        $inputOutput = $this->createMock(InputOutputInterface::class);

        //Act
        $fileNormalizersExecutor->execute($inputOutput, true);
    }

    /**
     * @return void
     */
    public function testExecuteShouldCallNormalizerWhenNormalizerIsApplicable(): void
    {
        //Arrange & Assert
        $fileNormalizerMock = $this->createNormalizerMock(true, true);
        $fileStorage = new FileStorage();
        $fileStorage->addFile('someClass.php');
        $fileNormalizersExecutor = new FileNormalizersExecutor($fileStorage, [$fileNormalizerMock]);
        $inputOutput = $this->createMock(InputOutputInterface::class);

        //Act
        $fileNormalizersExecutor->execute($inputOutput, false);
    }

    /**
     * @return void
     */
    public function testExecuteShouldPutErrorMessageWhenErrorsMessageIsDefined(): void
    {
        $errorMessage = 'File normalizer error message';
        $fileNormalizerMock = $this->createNormalizerMock(false, false, $errorMessage);
        $fileStorage = new FileStorage();
        $fileStorage->addFile('someClass.php');
        $fileNormalizersExecutor = new FileNormalizersExecutor($fileStorage, [$fileNormalizerMock]);
        $inputOutput = $this->createMock(InputOutputInterface::class);
        $inputOutput->expects($this->once())->method('warning')->with($errorMessage);

        //Act
        $fileNormalizersExecutor->execute($inputOutput, false);
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
