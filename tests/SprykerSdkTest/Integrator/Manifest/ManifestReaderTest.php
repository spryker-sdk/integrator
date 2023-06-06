<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Manifest;

use SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface;
use SprykerSdkTest\Integrator\BaseTestCase;

class ManifestReaderTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const MANIFESTS_CUSTOM_SOURCE = './tests/_data/manifests/src/integrator-manifests-master/';

    /**
     * @var string
     */
    protected const MANIFESTS_DIR_PATH = '_data/manifests/src';

    /**
     * @var string
     */
    protected const ZIP_PATH = '_data/manifests/archive.zip';

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $zipPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::ZIP_PATH;
        $dirPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::MANIFESTS_DIR_PATH;

        parent::zipDir($dirPath, $zipPath);
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->prepareTestEnv();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->removeTmpDirectory();
        $this->removeTmpManifestsArchive();
    }

    /**
     * @group ManifestReaderTest
     *
     * @return void
     */
    public function testReadsManifestsFromDefaultSource(): void
    {
        $commandArgumentsTransfer = $this->createCommandArgumentsTransfer();
        $commandArgumentsTransfer->setModules([$this->getModuleTransfer('Spryker.TestIntegratorWirePlugin')]);
        $manifestReader = $this->createManifestReader();

        $manifests = $manifestReader->readUnappliedManifests(
            $commandArgumentsTransfer,
            [],
        );

        $this->assertNotEmpty($manifests);
    }

    /**
     * @group ManifestReaderTest
     *
     * @return void
     */
    public function testReadsManifestsFromGivenSource(): void
    {
        $commandArgumentsTransfer = $this->createCommandArgumentsTransfer();
        $commandArgumentsTransfer->setSource(static::MANIFESTS_CUSTOM_SOURCE);
        $commandArgumentsTransfer->setModules([$this->getModuleTransfer('Spryker.TestIntegratorWirePlugin')]);
        $manifestReader = $this->createManifestReader();

        $manifests = $manifestReader->readUnappliedManifests(
            $commandArgumentsTransfer,
            [],
        );

        $this->assertNotEmpty($manifests);
    }

    /**
     * @return \SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface
     */
    protected function createManifestReader(): RepositoryManifestReaderInterface
    {
        return $this->getFactory()->createRepositoryManifestReader();
    }

    /**
     * @return void
     */
    private function prepareTestEnv(): void
    {
        $this->removeTmpDirectory();
        $this->createTmpDirectory();
        $this->createTmpStandaloneModulesDirectory();
        $this->copyProjectMockToTmpDirectory();
    }

    /**
     * @return void
     */
    private function createTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();

        if (!$fileSystem->exists($tmpPath)) {
            $fileSystem->mkdir($tmpPath, 0700);
        }
    }

    /**
     * @return void
     */
    private function createTmpStandaloneModulesDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $path = $this->getTempStandaloneModulesDirectoryPath();

        if (!$fileSystem->exists($path)) {
            $fileSystem->mkdir($path, 0700);
        }
    }

    /**
     * @return void
     */
    private function removeTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();

        if ($fileSystem->exists($tmpPath)) {
            $fileSystem->remove($tmpPath);
        }
    }

    /**
     * @return void
     */
    private function removeTmpManifestsArchive(): void
    {
        $fileSystem = $this->createFilesystem();
        $zipPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::ZIP_PATH;

        if ($fileSystem->exists($zipPath)) {
            $fileSystem->remove($zipPath);
        }
    }

    /**
     * @return void
     */
    private function copyProjectMockToTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();
        $projectMockPath = $this->getProjectMockPath();

        if ($fileSystem->exists($this->getTempDirectoryPath())) {
            $fileSystem->mirror($projectMockPath, $tmpPath);
        }
    }
}
