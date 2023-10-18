<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\Resolver;

use PhpParser\Node\Stmt\ClassConst;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface;
use SprykerSdk\Integrator\Builder\Resolver\PrefixedConstNameResolver;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class PrefixedConstNameResolverTest extends TestCase
{
    /**
     * @return void
     */
    public function testResolveClassConstantNameShouldReturnInitialNameWhenTargetClassNotSet(): void
    {
        // Arrange
        $classInformationTransfer = new ClassInformationTransfer();
        $prefixedConstNameResolver = new PrefixedConstNameResolver(
            $this->createClassConstantFinderMock(),
            $this->createClassLoaderMock(new ClassInformationTransfer()),
        );

        // Act
        $constName = $prefixedConstNameResolver->resolveClassConstantName($classInformationTransfer, 'static', 'TEST_CONST');

        // Assert
        $this->assertSame('TEST_CONST', $constName);
    }

    /**
     * @return void
     */
    public function testResolveClassConstantNameShouldReturnInitialNameWhenClassDoesNotExists(): void
    {
        // Arrange
        $classInformationTransfer = (new ClassInformationTransfer())->setClassName('TestClass');
        $prefixedConstNameResolver = new PrefixedConstNameResolver(
            $this->createClassConstantFinderMock(),
            $this->createClassLoaderMock(new ClassInformationTransfer(), false),
        );

        // Act
        $constName = $prefixedConstNameResolver->resolveClassConstantName($classInformationTransfer, 'static', 'TEST_CONST');

        // Assert
        $this->assertSame('TEST_CONST', $constName);
    }

    /**
     * @return void
     */
    public function testResolveClassConstantNameShouldReturnInitialNameWhenConstantNotFound(): void
    {
        // Arrange
        $classInformationTransfer = (new ClassInformationTransfer())->setClassName('TestClass');
        $prefixedConstNameResolver = new PrefixedConstNameResolver(
            $this->createClassConstantFinderMock(false),
            $this->createClassLoaderMock(new ClassInformationTransfer()),
        );

        // Act
        $constName = $prefixedConstNameResolver->resolveClassConstantName($classInformationTransfer, 'static', 'TEST_CONST');

        // Assert
        $this->assertSame('TEST_CONST', $constName);
    }

    /**
     * @return void
     */
    public function testResolveClassConstantNameShouldReturnPrefixedNameWhenConstantFound(): void
    {
        // Arrange
        $classInformationTransfer = (new ClassInformationTransfer())->setClassName('TestClass');
        $prefixedConstNameResolver = new PrefixedConstNameResolver(
            $this->createClassConstantFinderMock(),
            $this->createClassLoaderMock(new ClassInformationTransfer()),
        );

        // Act
        $constName = $prefixedConstNameResolver->resolveClassConstantName($classInformationTransfer, 'static', 'TEST_CONST');

        // Assert
        $this->assertSame('PYZ_TEST_CONST', $constName);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $loadedClass
     * @param bool $classExists
     *
     * @return \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface
     */
    protected function createClassLoaderMock(ClassInformationTransfer $loadedClass, bool $classExists = true): ClassLoaderInterface
    {
        $classLoader = $this->createMock(ClassLoaderInterface::class);
        $classLoader->method('classExist')->willReturn($classExists);
        $classLoader->method('loadClass')->willReturn($loadedClass);

        return $classLoader;
    }

    /**
     * @param bool $findConst
     *
     * @return \SprykerSdk\Integrator\Builder\Finder\ClassConstantFinderInterface
     */
    protected function createClassConstantFinderMock(bool $findConst = true): ClassConstantFinderInterface
    {
        $classConstantFinder = $this->createMock(ClassConstantFinderInterface::class);
        $classConstantFinder->method('findConstantByName')->willReturn($findConst ? new ClassConst([]) : null);

        return $classConstantFinder;
    }
}
