<?php

namespace SprykerSdkTest\Integrator\Business\Composer;


use SprykerSdk\Integrator\Business\Composer\ComposerLockReader;
use SprykerSdkTest\Integrator\BaseTestCase;

class ComposerLockReaderTest extends BaseTestCase
{
    private const DEFAULT_PACKAGE_NAME = "Nikic.PhpParser";

    /**
     *
     */
    public function testGetModuleVersions()
    {
        $composerLockReader = $this->createComposerLockReadr();

        $this->assertTrue(count($composerLockReader->getModuleVersions()) > 0);
        $this->assertArrayHasKey(self::DEFAULT_PACKAGE_NAME, $composerLockReader->getModuleVersions());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Composer\ComposerLockReader
     */
    private function createComposerLockReadr(): ComposerLockReader
    {
        return  new ComposerLockReader($this->getIntegratorConfig());
    }
}
