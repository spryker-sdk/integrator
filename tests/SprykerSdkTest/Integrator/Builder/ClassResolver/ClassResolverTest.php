<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\ClassResolver;

use ReflectionClass;
use SprykerSdk\Integrator\Builder\ClassGenerator\ClassGeneratorInterface;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface;
use SprykerSdk\Integrator\Builder\ClassResolver\ClassResolver;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassResolverTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testClearGeneratedClassListShouldClearGeneratedClassList(): void
    {
        // Arrange
        $classResolver = new ClassResolver(
            $this->createClassLoaderMock(new ClassInformationTransfer()),
            $this->createMock(ClassGeneratorInterface::class),
        );

        // Act & Assert
        $classResolver->resolveClass(static::class);

        $this->assertCount(1, $this->getGeneratedClassList());

        ClassResolver::clearGeneratedClassList();

        $this->assertCount(0, $this->getGeneratedClassList());
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     *
     * @return \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface
     */
    protected function createClassLoaderMock(ClassInformationTransfer $classInformationTransfer): ClassLoaderInterface
    {
        $classLoader = $this->createMock(ClassLoaderInterface::class);
        $classLoader->method('classExist')->willReturn(true);
        $classLoader->method('loadClass')->willReturn($classInformationTransfer);

        return $classLoader;
    }

    /**
     * @return array
     */
    protected function getGeneratedClassList(): array
    {
        return (new ReflectionClass(ClassResolver::class))->getStaticPropertyValue('generatedClassList');
    }
}
