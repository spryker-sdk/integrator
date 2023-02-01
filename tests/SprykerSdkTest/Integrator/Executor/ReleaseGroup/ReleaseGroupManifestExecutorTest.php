<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Executor\ReleaseGroup;

use RuntimeException;
use SprykerSdk\Integrator\Executor\ManifestExecutor;
use SprykerSdk\Integrator\Executor\ManifestExecutorInterface;
use SprykerSdk\Integrator\Executor\ReleaseGroup\DiffGenerator;
use SprykerSdk\Integrator\FileStorage\BucketFileStorage;
use SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface;
use SprykerSdk\Integrator\Manifest\FileBucketManifestReader;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use SprykerSdk\Integrator\VersionControlSystem\GitRepository;
use SprykerSdkTest\Integrator\BaseTestCase;

class ReleaseGroupManifestExecutorTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const INSTALLER_MANIFEST_JSON_PATH = './tests/_data/bucket_storage/installer-manifest.json';

    /**
     * @return void
     */
    public function testRunReleaseGroupManifestExecutionSuccess(): void
    {
        // Arrange
        $fileStorageMock = $this->createFileStorageMock(file_get_contents(static::INSTALLER_MANIFEST_JSON_PATH));
        $reader = new FileBucketManifestReader($fileStorageMock);

        $gitMock = $this->createMock(GitRepository::class);
        $gitMock->method('hasChanges')->willReturn(true);

        $executorMock = $this->createManifestExecutorMock();
        $manifestExecutor = new DiffGenerator($reader, $fileStorageMock, $executorMock, $gitMock);

        // Assert
        $gitMock->expects($this->atLeastOnce())->method('checkout');
        $gitMock->expects($this->once())->method('commit');
        $fileStorageMock->expects($this->once())->method('addFile');

        // Act
        $manifestExecutor->generateDiff(
            $this->createCommandArgumentsTransfer(),
            $this->buildSymfonyConsoleInputOutputAdapter(),
        );
    }

    /**
     * @return void
     */
    public function testRunReleaseGroupManifestExecutionSuccessDry(): void
    {
        // Arrange
        $fileStorageMock = $this->createFileStorageMock(file_get_contents(static::INSTALLER_MANIFEST_JSON_PATH));
        $reader = new FileBucketManifestReader($fileStorageMock);

        $gitMock = $this->createMock(GitRepository::class);
        $gitMock->method('hasChanges')->willReturn(true);

        $executorMock = $this->createManifestExecutorMock();
        $manifestExecutor = new DiffGenerator($reader, $fileStorageMock, $executorMock, $gitMock);

        // Assert
        $gitMock->expects($this->never())->method('checkout');
        $gitMock->expects($this->never())->method('commit');
        $fileStorageMock->expects($this->never())->method('addFile');

        // Act
        $manifestExecutor->generateDiff(
            $this->createCommandArgumentsTransfer(true),
            $this->buildSymfonyConsoleInputOutputAdapter(),
        );
    }

    /**
     * @return void
     */
    public function testRunReleaseGroupManifestExecutionFailedInvalidFileData(): void
    {
        // Arrange
        $gitMock = $this->createMock(GitRepository::class);
        $executorMock = $this->createMock(ManifestExecutor::class);
        $fileStorageMock = $this->createFileStorageMock('invalid json string');
        $reader = new FileBucketManifestReader($fileStorageMock);

        $manifestExecutor = new DiffGenerator($reader, $fileStorageMock, $executorMock, $gitMock);

        // Assert
        $this->expectException(RuntimeException::class);

        // Act
        $manifestExecutor->generateDiff(
            $this->createCommandArgumentsTransfer(),
            $this->buildSymfonyConsoleInputOutputAdapter(),
        );
    }

    /**
     * @return void
     */
    public function testRunReleaseGroupManifestExecutionFailedNoChangesHappened(): void
    {
        // Arrange
        $fileStorageMock = $this->createFileStorageMock(file_get_contents(static::INSTALLER_MANIFEST_JSON_PATH));
        $reader = new FileBucketManifestReader($fileStorageMock);

        $gitMock = $this->createMock(GitRepository::class);
        $gitMock->method('hasChanges')->willReturn(false);

        $executorMock = $this->createManifestExecutorMock();
        $manifestExecutor = new DiffGenerator($reader, $fileStorageMock, $executorMock, $gitMock);

        // Assert
        $this->expectException(RuntimeException::class);

        // Act
        $manifestExecutor->generateDiff(
            $this->createCommandArgumentsTransfer(),
            $this->buildSymfonyConsoleInputOutputAdapter(),
        );
    }

    /**
     * @param string $fileData
     *
     * @return \SprykerSdk\Integrator\FileStorage\BucketFileStorageInterface
     */
    protected function createFileStorageMock(string $fileData): BucketFileStorageInterface
    {
        $fileStorageMock = $this->createMock(BucketFileStorage::class);
        $fileStorageMock->method('getFile')->willReturn($fileData);

        return $fileStorageMock;
    }

    /**
     * @return \SprykerSdk\Integrator\Executor\ManifestExecutorInterface
     */
    protected function createManifestExecutorMock(): ManifestExecutorInterface
    {
        return $this->getMockBuilder(ManifestExecutor::class)
            ->onlyMethods(['applyManifestList'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param bool $isDry
     *
     * @return \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer
     */
    public function createCommandArgumentsTransfer(bool $isDry = false): IntegratorCommandArgumentsTransfer
    {
        $transfer = parent::createCommandArgumentsTransfer($isDry);
        $transfer->setReleaseGroupId(1);
        $transfer->setBranchToCompare('master');

        return $transfer;
    }
}
