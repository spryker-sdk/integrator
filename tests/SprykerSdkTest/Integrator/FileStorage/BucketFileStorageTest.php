<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\FileStorage;

use RuntimeException;
use SprykerSdk\Integrator\FileStorage\BucketFileStorage;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdkTest\Integrator\BaseTestCase;

class BucketFileStorageTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testSuccessOnInvalidFile(): void
    {
        // Arrange
        $fileStorageMock = $this->createMock(BucketFileStorage::class);

        // Act
        $result = $fileStorageMock->getFile('some-path.txt');

        // Assert
        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function testFailOnInvalidBucket(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Environment variable "INTEGRATOR_FILE_BUCKET_NAME" is not set.');

        // Arrange
        $configMock = $this->createMock(IntegratorConfig::class);
        $fileStorage = new BucketFileStorage($configMock);

        // Act
        $fileStorage->getFile('some-path.txt');
    }

    /**
     * @return void
     */
    public function testFailOnInvalidCredentialsKey(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Environment variable "INTEGRATOR_FILE_BUCKET_CREDENTIALS_KEY" is not set.');

        // Arrange
        $configMock = $this->createMock(IntegratorConfig::class);
        $configMock->method('getFileBucketName')->willReturn('string');
        $fileStorage = new BucketFileStorage($configMock);

        // Act
        $fileStorage->getFile('some-path.txt');
    }

    /**
     * @return void
     */
    public function testFailOnInvalidCredentialsSecret(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Environment variable "INTEGRATOR_FILE_BUCKET_CREDENTIALS_SECRET" is not set.');

        // Arrange
        $configMock = $this->createMock(IntegratorConfig::class);
        $configMock->method('getFileBucketName')->willReturn('string');
        $configMock->method('getFileBucketCredentialsKey')->willReturn('string');
        $fileStorage = new BucketFileStorage($configMock);

        // Act
        $fileStorage->getFile('some-path.txt');
    }

    /**
     * @return void
     */
    public function testFailOnInvalidBucketRegion(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Environment variable "INTEGRATOR_FILE_BUCKET_REGION" is not set.');

        // Arrange
        $configMock = $this->createMock(IntegratorConfig::class);
        $configMock->method('getFileBucketName')->willReturn('string');
        $configMock->method('getFileBucketCredentialsKey')->willReturn('string');
        $configMock->method('getFileBucketCredentialsSecret')->willReturn('string');
        $fileStorage = new BucketFileStorage($configMock);

        // Act
        $fileStorage->getFile('some-path.txt');
    }
}
