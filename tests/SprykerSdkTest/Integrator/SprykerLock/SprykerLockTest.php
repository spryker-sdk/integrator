<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator\SprykerLock;

use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\SprykerLock\SprykerLockReader;
use SprykerSdk\Integrator\SprykerLock\SprykerLockWriter;
use SprykerSdkTest\Integrator\BaseTestCase;

class SprykerLockTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testWriteFileLock(): void
    {
        $compareFilePath = './tests/_tests_files/spryker_lock_test_write_lock.json';
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

        $sprykerLockWriter = $this->createSprykerLockWriter($tmpIntegratorLockFilePath);
        $sprykerLockWriter->storeLock($lockData);

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
        $compareFilePath = './tests/_tests_files/spryker_lock_test_write_lock.json';

        file_put_contents($tmpIntegratorLockFilePath, file_get_contents($compareFilePath));

        $sprykerLockReader = $this->createSprykerLockReader($tmpIntegratorLockFilePath);
        $lockData = $sprykerLockReader->getLockFileData();

        $this->assertArrayHasKey('Spryker.Test', $lockData);
        $this->assertArrayHasKey('wire-plugin', $lockData['Spryker.Test']);

        $this->removeFile($tmpIntegratorLockFilePath);
    }

    /**
     * @param string $tmpIntegratorLockFilePath
     *
     * @return \SprykerSdk\Integrator\SprykerLock\SprykerLockWriter
     */
    private function createSprykerLockWriter(string $tmpIntegratorLockFilePath): SprykerLockWriter
    {
        $integrotorConfigMock = $this->createMock(IntegratorConfig::class);

        $integrotorConfigMock->method('getIntegratorLockFilePath')
            ->willReturn($tmpIntegratorLockFilePath);

        return new SprykerLockWriter($integrotorConfigMock);
    }

    /**
     * @param string $tmpIntegratorLockFilePath
     *
     * @return \SprykerSdk\Integrator\SprykerLock\SprykerLockReader
     */
    private function createSprykerLockReader(string $tmpIntegratorLockFilePath): SprykerLockReader
    {
        $integrotorConfigMock = $this->createMock(IntegratorConfig::class);

        $integrotorConfigMock->method('getIntegratorLockFilePath')
            ->willReturn($tmpIntegratorLockFilePath);

        return new SprykerLockReader($integrotorConfigMock);
    }

    /**
     * @return void
     */
    private function removeFile(string $path): void
    {
        $this->createFilesystem()->remove($path);
    }
}
