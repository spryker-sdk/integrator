<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\IntegratorFactoryAwareTrait;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class BaseTestCase extends PHPUnitTestCase
{
    use IntegratorFactoryAwareTrait;

    /**
     * @return \SprykerSdk\Integrator\IntegratorConfig
     */
    public function getIntegratorConfig(): IntegratorConfig
    {
        return IntegratorConfig::getInstance();
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    public function createFilesystem(): Filesystem
    {
        return new Filesystem();
    }

    /**
     * @return string
     */
    public function getTempDirectoryPath(): string
    {
        return APPLICATION_ROOT_DIR;
    }

    /**
     * @return string
     */
    public function getTempStandaloneModulesDirectoryPath(): string
    {
        return APPLICATION_STANDALONE_MODULES_DIR;
    }

    /**
     * @return string
     */
    public function getDataDirectoryPath(): string
    {
        return ROOT_TESTS . DIRECTORY_SEPARATOR . DATA_DIRECTORY_NAME;
    }

    /**
     * @return string
     */
    public function getProjectMockPath(): string
    {
        return $this->getDataDirectoryPath() . DIRECTORY_SEPARATOR . 'project_mock';
    }

    /**
     * @param string $dirPath
     * @param string $zipPath
     *
     * @return void
     */
    public static function zipDir(string $dirPath, string $zipPath): void
    {
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        /** @var array<\SplFileInfo> $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath),
            RecursiveIteratorIterator::LEAVES_ONLY,
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dirPath) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }
}
