<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\ClassLoader;

use Exception;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer as TransferClassInformationTransfer;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassLoaderTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testLoadClassWithoutParent(): void
    {
        // Arrange & Act
        $transfer = $this->getFactory()->createClassLoader()->loadClass(Exception::class);

        // Assert
        $this->assertEquals(Exception::class, $transfer->getClassName());
        $this->assertNull($transfer->getParent());
    }

    /**
     * @return void
     */
    public function testLoadClassWithParent(): void
    {
        // Arrange & Act
        $transfer = $this->getFactory()->createClassLoader()->loadClass(static::class);

        // Assert
        $this->assertEquals(static::class, $transfer->getClassName());
        $this->assertInstanceOf(TransferClassInformationTransfer::class, $transfer->getParent());
    }

    /**
     * @return void
     */
    public function testLoadClassWithNotExistingFile(): void
    {
        // Arrange & Act
        $transfer = $this->getFactory()->createClassLoader()->loadClass('\\Bla\\BlaBla\\Test');

        // Assert
        $this->assertEquals('Bla\\BlaBla\\Test', $transfer->getClassName());
        $this->assertNull($transfer->getParent());
        $this->assertNull($transfer->getFilePath());
        $this->assertSame([], $transfer->getTokens());
        $this->assertSame([], $transfer->getOriginalTokenTree());
    }

    /**
     * @return void
     */
    public function testClassExist(): void
    {
        // Arrange & Act
        $isExists = $this->getFactory()->createClassLoader()->classExist(static::class);

        // Assert
        $this->assertTrue($isExists);
    }

    /**
     * @return void
     */
    public function testClassNotExist(): void
    {
        // Arrange & Act
        $isExists = $this->getFactory()->createClassLoader()->classExist('\\Bla\\BlaBla\\Test');

        // Assert
        $this->assertFalse($isExists);
    }
}
