<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassResolver;

use SprykerSdk\Integrator\Builder\ClassGenerator\ClassGeneratorInterface;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassResolver implements ClassResolverInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface
     */
    protected ClassLoaderInterface $classLoader;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassGenerator\ClassGeneratorInterface
     */
    protected ClassGeneratorInterface $classGenerator;

    /**
     * @var array<\SprykerSdk\Integrator\Transfer\ClassInformationTransfer>
     */
    protected static array $generatedClassList = [];

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface $classLoader
     * @param \SprykerSdk\Integrator\Builder\ClassGenerator\ClassGeneratorInterface $classGenerator
     */
    public function __construct(
        ClassLoaderInterface $classLoader,
        ClassGeneratorInterface $classGenerator
    ) {
        $this->classLoader = $classLoader;
        $this->classGenerator = $classGenerator;
    }

    /**
     * @param string $targetClassName
     * @param string $customOrganisation
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function resolveClass(string $targetClassName, string $customOrganisation = ''): ClassInformationTransfer
    {
        $resolvedClassName = $targetClassName;
        if ($customOrganisation) {
            $resolvedClassName = (string)preg_replace("/(\w+)/", $customOrganisation, $targetClassName, 1);
        }

        if (!isset(static::$generatedClassList[$resolvedClassName])) {
            static::$generatedClassList[$resolvedClassName] = $this->loadOrCreateClassInformationTransfer(
                $resolvedClassName,
                $targetClassName,
            );
        }

        return static::$generatedClassList[$resolvedClassName];
    }

    /**
     * @param string $resolvedClassName
     * @param string $targetClassName
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    protected function loadOrCreateClassInformationTransfer(
        string $resolvedClassName,
        string $targetClassName
    ): ClassInformationTransfer {
        if ($this->classLoader->classExist($resolvedClassName)) {
            return $this->classLoader->loadClass($resolvedClassName);
        }

        return $this->classGenerator->generateClass($resolvedClassName, $targetClassName);
    }
}
