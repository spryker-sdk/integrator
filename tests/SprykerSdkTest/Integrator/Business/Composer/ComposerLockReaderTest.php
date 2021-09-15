<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator\Business\Composer;

use SprykerSdk\Integrator\Business\Composer\ComposerLockReader;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdkTest\Integrator\BaseTestCase;

class ComposerLockReaderTest extends BaseTestCase
{
    private const DEFAULT_PACKAGE_NAME = 'Nikic.PhpParser';

    /**
     * @return void
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
        $integratorConfigMock = $this->createMock(IntegratorConfig::class);
        $integratorConfigMock->method('getComposerLockFilePath')->willReturn('./composer.lock');

        return new ComposerLockReader($integratorConfigMock);
    }
}
