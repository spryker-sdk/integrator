<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\IntegratorLock;

use PHPUnit\Framework\MockObject\MockObject;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockCleaner;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockReader;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriter;
use SprykerSdkTest\Integrator\BaseTestCase;

class IntegratorLockTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testWriteFileLock(): void
    {
        $compareFilePath = ROOT_TESTS . '/_data/composer/spryker_lock_test_write_lock.json';
        $lockData = [
            'Spryker.Test' => [
                'wire-plugin' => [
                    '111111aaaaaabb' => [
                        'target' => "\Spryker\Zed\Test\TestDependencyProvider::getTestPlugins",
                        'source' => "\Spryker\Zed\Test\Communication\Plugin\TestPlugin",
                    ],
                ],
            ],
        ];
        $tmpIntegratorLockFilePath = tempnam(sys_get_temp_dir(), 'integrator.lock.');

        $integratorLockWriter = $this->createIntegratorLockWriter($tmpIntegratorLockFilePath);
        $integratorLockWriter->storeLock($lockData);

        $this->assertFileExists($tmpIntegratorLockFilePath);
        $this->assertFileExists($compareFilePath);

        $this->assertJsonFileEqualsJsonFile($compareFilePath, $tmpIntegratorLockFilePath);

        $this->removeFile($tmpIntegratorLockFilePath);
    }

    /**
     * @return void
     */
    public function testReadFileLock(): void
    {
        $tmpIntegratorLockFilePath = tempnam(sys_get_temp_dir(), 'integrator.lock.');
        $compareFilePath = ROOT_TESTS . '/_data/composer/spryker_lock_test_write_lock.json';

        file_put_contents($tmpIntegratorLockFilePath, file_get_contents($compareFilePath));

        $integratorLockReader = $this->createIntegratorLockReader($tmpIntegratorLockFilePath);
        $lockData = $integratorLockReader->getLockFileData();

        $this->assertArrayHasKey('Spryker.Test', $lockData);
        $this->assertArrayHasKey('wire-plugin', $lockData['Spryker.Test']);

        $this->removeFile($tmpIntegratorLockFilePath);
    }

    /**
     * @return void
     */
    public function testDeleteFileLock(): void
    {
        $tmpIntegratorLockFilePath = tempnam(sys_get_temp_dir(), 'integrator.lock.');
        $compareFilePath = ROOT_TESTS . '/_data/composer/spryker_lock_test_write_lock.json';

        file_put_contents($tmpIntegratorLockFilePath, file_get_contents($compareFilePath));

        $integratorLockCleaner = $this->createIntegratorLockCleaner($tmpIntegratorLockFilePath);
        $integratorLockCleaner->deleteLock();

        $this->assertFileDoesNotExist($tmpIntegratorLockFilePath);
    }

    /**
     * @param string $tmpIntegratorLockFilePath
     *
     * @return \SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriter
     */
    private function createIntegratorLockWriter(string $tmpIntegratorLockFilePath): IntegratorLockWriter
    {
        return new IntegratorLockWriter($this->mockIntegratorConfig($tmpIntegratorLockFilePath));
    }

    /**
     * @param string $tmpIntegratorLockFilePath
     *
     * @return \SprykerSdk\Integrator\IntegratorLock\IntegratorLockReader
     */
    private function createIntegratorLockReader(string $tmpIntegratorLockFilePath): IntegratorLockReader
    {
        return new IntegratorLockReader($this->mockIntegratorConfig($tmpIntegratorLockFilePath));
    }

    /**
     * @param string $tmpIntegratorLockFilePath
     *
     * @return \SprykerSdk\Integrator\IntegratorLock\IntegratorLockCleaner
     */
    private function createIntegratorLockCleaner(string $tmpIntegratorLockFilePath): IntegratorLockCleaner
    {
        return new IntegratorLockCleaner($this->mockIntegratorConfig($tmpIntegratorLockFilePath));
    }

    /**
     * @param string $tmpIntegratorLockFilePath
     *
     * @return \SprykerSdk\Integrator\IntegratorConfig
     */
    private function mockIntegratorConfig(string $tmpIntegratorLockFilePath): IntegratorConfig
    {
        $integratorConfigMock = $this->createMock(IntegratorConfig::class);

        $integratorConfigMock->method('getIntegratorLockFilePath')
            ->willReturn($tmpIntegratorLockFilePath);

        return $integratorConfigMock;
    }

    /**
     * @param string $path
     *
     * @return void
     */
    private function removeFile(string $path): void
    {
        $this->createFilesystem()->remove($path);
    }
}
