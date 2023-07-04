<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator;

use Symfony\Component\Filesystem\Filesystem;

class IntegratorFacadeGeneralTest extends AbstractIntegratorTestCase
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
     * Rewrite the path to mock project
     *
     * @return string
     */
    public function getProjectMockOriginalPath(): string
    {
        return $this->getDataDirectoryPath() . DIRECTORY_SEPARATOR . 'general' . DIRECTORY_SEPARATOR . 'project_original';
    }

    /**
     * Rewrite the path to mock project after integration
     *
     * @return string
     */
    public function getProjectMockCurrentPath(): string
    {
        return $this->getDataDirectoryPath() . DIRECTORY_SEPARATOR . 'general' . DIRECTORY_SEPARATOR . 'project_current';
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
        $testFilePath = $this->getProjectMockCurrentPath() . $glossaryPath;
        $testResultFile = $this->getTestTmpDirPath() . $glossaryPath;

        $this->assertFileExists($testFilePath);
        $this->assertFileExists($testResultFile);
        $this->assertStringContainsString(
            trim(file_get_contents($this->getProjectMockOriginalPath() . $glossaryPath)),
            trim(file_get_contents($testResultFile)),
        );
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($testResultFile)));
    }
}
