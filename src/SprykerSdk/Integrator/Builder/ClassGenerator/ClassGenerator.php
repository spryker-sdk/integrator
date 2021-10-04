<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassGenerator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Name;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoader;
use SprykerSdk\Integrator\Common\UtilText\Filter\CamelCaseToSeparator;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ClassGenerator implements ClassGeneratorInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoader
     */
    protected $classLoader;

    /**
     * @var \SprykerSdk\Integrator\Helper\ClassHelperInterface
     */
    protected $classHelper;

    /**
     * @var \PhpParser\BuilderFactory
     */
    protected $builderFactory;

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @var \PhpParser\Builder\Class_
     */
    protected $classBuilder;

    /**
     * @var \PhpParser\Builder\Namespace_
     */
    protected $classNamespaceBuilder;

    /**
     * @param \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoader $classLoader
     * @param \SprykerSdk\Integrator\Helper\ClassHelperInterface $classHelper
     * @param \PhpParser\BuilderFactory $builderFactory
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     */
    public function __construct(
        ClassLoader $classLoader,
        ClassHelperInterface $classHelper,
        BuilderFactory $builderFactory,
        IntegratorConfig $config
    ) {
        $this->classLoader = $classLoader;
        $this->builderFactory = $builderFactory;
        $this->classHelper = $classHelper;
        $this->config = $config;
    }

    /**
     * @param string $className
     * @param string|null $parentClass
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function generateClass(string $className, ?string $parentClass = null): ?ClassInformationTransfer
    {
        $classInformationTransfer = (new ClassInformationTransfer())
            ->setClassName($className);

        $moduleDir = $this->resolveModuleDir(
            $this->classHelper->getOrganisationName($className),
            $this->classHelper->getModuleName($className)
        );

        $this->classBuilder = $this->builderFactory->class($this->classHelper->getShortClassName($className));
        $this->classNamespaceBuilder = $this->builderFactory
            ->namespace(ltrim($this->classHelper->getClassNamespace($className), '\\'))
            ->setDocComment($this->findLicenseBlock($moduleDir));

        if ($parentClass) {
            $this->extendWithParentClass($className, $parentClass);

            $classInformationTransfer->setParent(
                $this->classLoader->loadClass($parentClass)
            );
        }

        $this->classNamespaceBuilder->addStmt($this->classBuilder);

        $syntaxTree = [$this->classNamespaceBuilder->getNode()];

        $classInformationTransfer->setClassTokenTree($syntaxTree)
            ->setFilePath(
                $moduleDir
                . '/src'
                . $this->convertClassNameToPath($className)
                . '.php'
            );

        return $classInformationTransfer;
    }

    /**
     * @param string $className
     * @param string $parentClass
     *
     * @return void
     */
    protected function extendWithParentClass(string $className, string $parentClass): void
    {
        $parentClassAlias = $this->classHelper->getShortClassName($parentClass);
        if ($parentClassAlias === $this->classHelper->getShortClassName($className)) {
            $parentClassAlias = $this->classHelper->getOrganisationName($parentClass) . $this->classHelper->getShortClassName($parentClass);
        }

        $use = $this->builderFactory->use(ltrim($parentClass, '\\'));
        if ($parentClassAlias) {
            $use = $use->as($parentClassAlias);
        }

        $this->classNamespaceBuilder = $this->classNamespaceBuilder->addStmt($use);
        $this->classBuilder = $this->classBuilder->extend(new Name($parentClassAlias, []));
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
     * @return string|array<string>
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
