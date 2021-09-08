<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Business\Builder\ClassGenerator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Name;
use SprykerSdk\Integrator\Business\Builder\ClassLoader\ClassLoader;
use SprykerSdk\Integrator\Business\Helper\ClassHelper;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Shared\Common\UtilText\Filter\CamelCaseToSeparator;
use SprykerSdk\Shared\Transfer\ClassInformationTransfer;

class ClassGenerator
{
    /**
     * @var \SprykerSdk\Integrator\Business\Builder\ClassLoader\ClassLoader
     */
    protected $classLoader;

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @param \SprykerSdk\Integrator\Business\Builder\ClassLoader\ClassLoader $classLoader
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     */
    public function __construct(ClassLoader $classLoader, IntegratorConfig $config)
    {
        $this->classLoader = $classLoader;
        $this->config = $config;
    }

    /**
     * @param string $className
     * @param string|null $parentClass
     *
     * @return \SprykerSdk\Shared\Transfer\ClassInformationTransfer|null
     */
    public function generateClass(string $className, ?string $parentClass = null): ?ClassInformationTransfer
    {
        $classInformationTransfer = (new ClassInformationTransfer())
            ->setClassName($className);

        $factory = new BuilderFactory();
        $classHelper = new ClassHelper();

        $moduleDir = $this->resolveModuleDir(
            $classHelper->getOrganisationName($className),
            $classHelper->getModuleName($className)
        );
        $classBuilder = $factory->class($classHelper->getShortClassName($className));
        $classNamespaceBuilder = $factory->namespace(ltrim($classHelper->getClassNamespace($className), '\\'))
            ->setDocComment($this->findLicenseBlock($moduleDir));

        if ($parentClass) {
            $parentClassAlias = $classHelper->getShortClassName($parentClass);
            if ($parentClassAlias === $classHelper->getShortClassName($className)) {
                $parentClassAlias = $classHelper->getOrganisationName($parentClass) . $classHelper->getShortClassName($parentClass);
            }
            $use = $factory->use(ltrim($parentClass, '\\'));
            if ($parentClassAlias) {
                $use = $use->as($parentClassAlias);
            }
            $classNamespaceBuilder = $classNamespaceBuilder->addStmt($use);
            $classBuilder = $classBuilder->extend(new Name($parentClassAlias, []));
        }

        $classNamespaceBuilder->addStmt($classBuilder);

        $ast = [$classNamespaceBuilder->getNode()];

        $classInformationTransfer->setClassTokenTree($ast)
            ->setFilePath(
                $moduleDir
                . '/src'
                . $this->convertClassNameToPath($className)
                . '.php'
            );

        if ($parentClass) {
            $classInformationTransfer->setParent(
                $this->classLoader->loadClass($parentClass)
            );
        }

        return $classInformationTransfer;
    }

    /**
     * @param string $organisation
     * @param string $module
     *
     * @return string
     */
    protected function resolveModuleDir(string $organisation, string $module): string
    {
        if (in_array($organisation, $this->config->getCoreNonSplitOrganisations())) {
            return $this->config->getCoreRootDirectory()
                . sprintf($this->config->getNonSplitRepositoryPathPattern(), $this->camelCaseToDash($organisation))
                . $module
                . DIRECTORY_SEPARATOR;
        }

        if (in_array($organisation, $this->config->getCoreNamespaces())) {
            return $this->config->getCoreRootDirectory()
                . $organisation
                . DIRECTORY_SEPARATOR
                . $module
                . DIRECTORY_SEPARATOR;
        }

        return $this->config->getProjectRootDirectory();
    }

    /**
     * @param string $moduleDir
     *
     * @return string
     */
    protected function findLicenseBlock(string $moduleDir): string
    {
        if (file_exists($moduleDir . '.license')) {
            return file_get_contents($moduleDir . '.license');
        }

        return '';
    }

    /**
     * @param string $className
     *
     * @return string|string[]
     */
    protected function convertClassNameToPath(string $className)
    {
        return str_replace('\\', '/', $className);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function camelCaseToDash(string $value): string
    {
        return (new CamelCaseToSeparator())->filter($value);
    }
}
