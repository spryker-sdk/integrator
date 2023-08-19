<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassResolver;

use RuntimeException;
use SprykerSdk\Integrator\Builder\ClassGenerator\ClassGeneratorInterface;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface;
use SprykerSdk\Integrator\Builder\ComposerClassLoader\ComposerClassLoader;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassResolver implements ClassResolverInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface
     */
    protected $classLoader;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassGenerator\ClassGeneratorInterface
     */
    protected $classGenerator;

    /**
     * @var array<\SprykerSdk\Integrator\Transfer\ClassInformationTransfer>
     */
    protected static $generatedClassList = [];

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
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function resolveClass(string $targetClassName, string $customOrganisation = ''): ?ClassInformationTransfer
    {
        $resolvedClassName = $targetClassName;
        if ($customOrganisation) {
            $resolvedClassName = (string)preg_replace("/(\w+)/", $customOrganisation, $targetClassName, 1);
        }

        if (!isset(static::$generatedClassList[$resolvedClassName])) {
            if (ComposerClassLoader::classExist($resolvedClassName)) {
                $classInformationTransfer = $this->classLoader->loadClass($resolvedClassName);
            } else {
                $classInformationTransfer = $this->classGenerator->generateClass($resolvedClassName, $targetClassName);
            }

            if ($classInformationTransfer === null) {
                throw new RuntimeException(sprintf('Cannot resolve `%s`', $targetClassName));
            }

            static::$generatedClassList[$resolvedClassName] = $classInformationTransfer;
        }

        return static::$generatedClassList[$resolvedClassName];
    }
}
