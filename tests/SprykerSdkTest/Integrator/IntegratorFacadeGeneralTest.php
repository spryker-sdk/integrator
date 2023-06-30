<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator;

use Symfony\Component\Filesystem\Filesystem;

class IntegratorFacadeGeneralTest extends AbstractIntegratorFacade
{
    /**
     * @var string
     */
    protected const MANIFESTS_DIR_PATH = 'general/manifests/src';

    /**
     * @var string
     */
    protected const ZIP_PATH = '_data/archive.zip';

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $zipPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::ZIP_PATH;
        $dirPath = DATA_PROVIDER_DIR . DIRECTORY_SEPARATOR . static::MANIFESTS_DIR_PATH;

        parent::zipDir($dirPath, $zipPath);
    }

    /**
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        $fs = new Filesystem();
        $zipPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::ZIP_PATH;
        $fs->remove($zipPath);
    }

    /**
     * @return void
     */
    public function testRunInstallationGlossary(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorGlossary')]),
        );

        // Assert
        $glossaryPath = '/data/import/common/common/glossary.csv';
        $testFilePath = $this->getProjectGeneralMockCurrentPath() . $glossaryPath;
        $testResultFile = $this->getTestTmpDirPath() . $glossaryPath;

        $this->assertFileExists($testFilePath);
        $this->assertFileExists($testResultFile);
        $this->assertStringContainsString(
            trim(file_get_contents($this->getProjectGeneralMockOriginalPath() . $glossaryPath)),
            trim(file_get_contents($testResultFile)),
        );
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($testResultFile)));
    }

    /**
     * @return void
     */
    protected function copyProjectMockToTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();
        $projectMockPath = $this->getProjectGeneralMockOriginalPath();

        if ($fileSystem->exists($this->getTempDirectoryPath())) {
            $fileSystem->mirror($projectMockPath, $tmpPath);
        }
    }
}
