<?php

namespace SprykerSdkTest\Integrator\Business\SprykerLock;

use SprykerSdk\Integrator\Business\SprykerLock\SprykerLockReader;
use SprykerSdk\Integrator\Business\SprykerLock\SprykerLockWriter;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdkTest\Integrator\BaseTestCase;

class SprykerLockTest extends BaseTestCase
{
    public function testWriteFileLock(): void
    {
        $compareFilePath = './tests/_tests_files/spryker_lock_test_write_lock.json';
        $lockData = [
            "Spryker.Test" => [
                "wire-plugin" => [
                    "111111aaaaaabb" => [
                        "target" => "\Spryker\Zed\Test\TestDependencyProvider::getTestPlugins",
                        "source" => "\Spryker\Zed\Test\Communication\Plugin\TestPlugin"
                    ]
                ]
            ]
        ];
        $tmpIntegratorLockFilePath = tempnam(sys_get_temp_dir(), 'integrator.lock.');

        $sprykerLockWriter = $this->createSprykerLockWriter($tmpIntegratorLockFilePath);
        $sprykerLockWriter->storeLock($lockData);


        $this->assertFileExists($tmpIntegratorLockFilePath);
        $this->assertFileExists($compareFilePath);

        $this->assertJsonFileEqualsJsonFile($compareFilePath, $tmpIntegratorLockFilePath);

        $this->removeFile($tmpIntegratorLockFilePath);
    }

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
     * @return \SprykerSdk\Integrator\Business\SprykerLock\SprykerLockReader
     */
    private function createSprykerLockReader(string $tmpIntegratorLockFilePath): SprykerLockReader
    {
        $integrotorConfigMock = $this->createMock(IntegratorConfig::class);

        $integrotorConfigMock->method('getIntegratorLockFilePath')
            ->willReturn($tmpIntegratorLockFilePath);

        return new SprykerLockReader($integrotorConfigMock);
    }

    private function removeFile(string $path): void
    {
        $this->createFilesystem()->remove($path);
    }
}
