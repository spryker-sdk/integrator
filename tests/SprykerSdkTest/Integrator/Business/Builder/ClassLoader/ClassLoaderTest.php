<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdkTest\Zed\Integrator\Business\Builder\ClassLoader;

use SprykerSdk\Integrator\Business\Builder\ClassLoader\ClassLoader;
use SprykerSdk\Integrator\Business\Helper\ClassHelper;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassLoaderTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testLoadClass(): void
    {
        $transfer = $this->createClassLoader()->loadClass(ClassHelper::class);

        $this->assertEquals(ClassHelper::class, $transfer->getClassName());
        $this->assertNull($transfer->getParent());
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Builder\ClassLoader\ClassLoader
     */
    private function createClassLoader(): ClassLoader
    {
        return new ClassLoader(
            $this->createPhpParser(),
            $this->createLexer()
        );
    }
}
