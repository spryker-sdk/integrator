<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator;

use SprykerSdk\Integrator\IntegratorFacade;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractIntegratorTestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected const MANIFESTS_DIR_PATH = '';

    /**
     * @var string
     */
    protected const ZIP_PATH = '';

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
        $this->clearTestEnv();
    }

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $zipPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::ZIP_PATH;
        $dirPath = DATA_PROVIDER_DIR . DIRECTORY_SEPARATOR . static::MANIFESTS_DIR_PATH;

        static::zipDir($dirPath, $zipPath);
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
     * @return \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    protected function createIntegratorFacade(): IntegratorFacade
    {
        return new IntegratorFacade();
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
    protected function copyProjectMockToTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();
        $projectMockPath = $this->getProjectMockOriginalPath();

        if ($fileSystem->exists($this->getTempDirectoryPath())) {
            $fileSystem->mirror($projectMockPath, $tmpPath);
        }
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
    private function clearTestEnv(): void
    {
        $this->removeTmpDirectory();
    }
}
