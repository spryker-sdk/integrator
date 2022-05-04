<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder\Module\ModuleFinder;

use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Filter\Word\DashToCamelCase;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcherInterface;
use SprykerSdk\Integrator\Transfer\ApplicationTransfer;
use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use SprykerSdk\Integrator\Transfer\OrganizationTransfer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ModuleFinder implements ModuleFinderInterface
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @var \SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcherInterface
     */
    protected $moduleMatcher;

    /**
     * @var array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    protected static $moduleTransferCollection;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcherInterface $moduleMatcher
     */
    public function __construct(IntegratorConfig $config, ModuleMatcherInterface $moduleMatcher)
    {
        $this->config = $config;
        $this->moduleMatcher = $moduleMatcher;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    public function getModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        if ($moduleFilterTransfer === null && static::$moduleTransferCollection !== null) {
            return static::$moduleTransferCollection;
        }

        $moduleTransferCollection = [];

        $moduleTransferCollection = $this->addStandaloneModulesToCollection($moduleTransferCollection, $moduleFilterTransfer);
        $moduleTransferCollection = $this->addModulesToCollection($moduleTransferCollection, $moduleFilterTransfer);

        ksort($moduleTransferCollection);

        if ($moduleFilterTransfer === null) {
            static::$moduleTransferCollection = $moduleTransferCollection;
        }

        return $moduleTransferCollection;
    }

    /**
     * @param array $moduleTransferCollection
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    protected function addStandaloneModulesToCollection(array $moduleTransferCollection, ?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        foreach ($this->getStandaloneModuleFinder() as $directoryInfo) {
            if (in_array($this->camelCase($directoryInfo->getFilename()), $this->config->getCoreNonSplitOrganisations(), true)) {
                continue;
            }

            $moduleTransfer = $this->getModuleTransfer($directoryInfo);
            $moduleTransfer->setIsStandalone(true);

            if (!$this->isModule($moduleTransfer)) {
                continue;
            }

            $moduleTransferCollection = $this->addModuleToCollection($moduleTransfer, $moduleTransferCollection, $moduleFilterTransfer);
        }

        return $moduleTransferCollection;
    }

    /**
     * @return \Symfony\Component\Finder\Finder<\Symfony\Component\Finder\SplFileInfo>
     */
    protected function getStandaloneModuleFinder(): Finder
    {
        return (new Finder())
            ->directories()
            ->depth('== 0')
            ->in($this->config->getVendorDirectory() . 'spryker/');
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransferCollection
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    protected function addModuleToCollection(
        ModuleTransfer $moduleTransfer,
        array $moduleTransferCollection,
        ?ModuleFilterTransfer $moduleFilterTransfer = null
    ): array {
        if ($moduleFilterTransfer !== null && !$this->moduleMatcher->matches($moduleTransfer, $moduleFilterTransfer)) {
            return $moduleTransferCollection;
        }

        if ($moduleFilterTransfer && $moduleFilterTransfer->getModule() !== null) {
            $moduleTransfer->setVersion($moduleFilterTransfer->getModule()->getVersion());
        }

        $moduleTransferCollection[$this->buildCollectionKey($moduleTransfer)] = $moduleTransfer;

        return $moduleTransferCollection;
    }

    /**
     * Modules which are standalone, can also be normal modules. This can be detected by the composer.json description
     * which contains `module` at the end of the description.
     *
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return bool
     */
    protected function isModule(ModuleTransfer $moduleTransfer): bool
    {
        $composerJsonAsArray = $this->getComposerJsonAsArray($moduleTransfer->getPathOrFail());

        if (!isset($composerJsonAsArray['description'])) {
            return false;
        }

        $description = $composerJsonAsArray['description'];

        return (bool)preg_match('/\smodule$/', $description);
    }

    /**
     * @param array $moduleTransferCollection
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null $moduleFilterTransfer
     *
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    protected function addModulesToCollection(array $moduleTransferCollection, ?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        foreach ($this->getModuleFinder() as $directoryInfo) {
            $moduleTransfer = $this->getModuleTransfer($directoryInfo);

            if (!$this->isModule($moduleTransfer)) {
                continue;
            }
            $moduleTransferCollection = $this->addModuleToCollection($moduleTransfer, $moduleTransferCollection, $moduleFilterTransfer);
        }

        return $moduleTransferCollection;
    }

    /**
     * @return \Symfony\Component\Finder\Finder<\Symfony\Component\Finder\SplFileInfo>
     */
    protected function getModuleFinder(): Finder
    {
        return (new Finder())
            ->directories()
            ->depth('== 0')
            ->in($this->getModuleDirectories());
    }

    /**
     * @return array
     */
    protected function getModuleDirectories(): array
    {
        return $this->config->getPathsToInternalOrganizations();
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function getModuleTransfer(SplFileInfo $directoryInfo): ModuleTransfer
    {
        if ($this->existComposerJson($directoryInfo->getPathname())) {
            return $this->buildModuleTransferFromComposerJsonInformation($directoryInfo);
        }

        return $this->buildModuleTransferFromDirectoryInformation($directoryInfo);
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return string
     */
    protected function buildCollectionKey(ModuleTransfer $moduleTransfer): string
    {
        return sprintf('%s.%s', $moduleTransfer->getOrganizationOrFail()->getNameOrFail(), $moduleTransfer->getNameOrFail());
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function existComposerJson(string $path): bool
    {
        $pathToComposerJson = sprintf('%s/composer.json', $path);

        return file_exists($pathToComposerJson);
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function buildModuleTransferFromDirectoryInformation(SplFileInfo $directoryInfo): ModuleTransfer
    {
        $organizationNameDashed = $this->getOrganizationNameFromDirectory($directoryInfo);
        $organizationName = $this->camelCase($organizationNameDashed);

        $moduleName = $this->camelCase($this->getModuleNameFromDirectory($directoryInfo));
        $moduleNameDashed = $this->dasherize($moduleName);

        $organizationTransfer = $this->buildOrganizationTransfer($organizationName, $organizationNameDashed);

        $moduleTransfer = $this->buildModuleTransfer($moduleName, $moduleNameDashed, $directoryInfo);
        $moduleTransfer
            ->setOrganization($organizationTransfer);

        $moduleTransfer = $this->addApplications($moduleTransfer);

        return $moduleTransfer;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function buildModuleTransferFromComposerJsonInformation(SplFileInfo $directoryInfo): ModuleTransfer
    {
        $composerJsonAsArray = $this->getComposerJsonAsArray($directoryInfo->getPathname());

        $organizationNameDashed = $this->getOrganizationNameFromComposer($composerJsonAsArray);
        $organizationName = $this->camelCase($organizationNameDashed);

        $moduleNameDashed = $this->getModuleNameFromComposer($composerJsonAsArray);
        $moduleName = $this->camelCase($moduleNameDashed);

        $organizationTransfer = $this->buildOrganizationTransfer($organizationName, $organizationNameDashed);

        $moduleTransfer = $this->buildModuleTransfer($moduleName, $moduleNameDashed, $directoryInfo);
        $moduleTransfer
            ->setOrganization($organizationTransfer);

        $moduleTransfer = $this->addApplications($moduleTransfer);

        return $moduleTransfer;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function addApplications(ModuleTransfer $moduleTransfer): ModuleTransfer
    {
        $lookupDirectory = sprintf('%s/src/%s/', $moduleTransfer->getPathOrFail(), $moduleTransfer->getOrganizationOrFail()->getNameOrFail());
        if (!is_dir($lookupDirectory)) {
            return $moduleTransfer;
        }
        $applicationFinder = new Finder();
        $applicationFinder->in($lookupDirectory)->depth('== 0');

        foreach ($applicationFinder as $applicationDirectoryInfo) {
            $applicationTransfer = new ApplicationTransfer();
            $applicationTransfer->setName($applicationDirectoryInfo->getRelativePathname());
            $moduleTransfer->addApplication($applicationTransfer);
        }

        return $moduleTransfer;
    }

    /**
     * @param string $organizationName
     * @param string $organizationNameDashed
     *
     * @return \SprykerSdk\Integrator\Transfer\OrganizationTransfer
     */
    protected function buildOrganizationTransfer(string $organizationName, string $organizationNameDashed): OrganizationTransfer
    {
        $organizationTransfer = new OrganizationTransfer();
        $organizationTransfer
            ->setName($organizationName)
            ->setNameDashed($organizationNameDashed);

        return $organizationTransfer;
    }

    /**
     * @param string $moduleName
     * @param string $moduleNameDashed
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function buildModuleTransfer(string $moduleName, string $moduleNameDashed, SplFileInfo $directoryInfo): ModuleTransfer
    {
        $moduleTransfer = new ModuleTransfer();
        $moduleTransfer
            ->setName($moduleName)
            ->setNameDashed($moduleNameDashed)
            ->setPath($directoryInfo->getRealPath() . DIRECTORY_SEPARATOR)
            ->setIsStandalone(false);

        return $moduleTransfer;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function getComposerJsonAsArray(string $path): array
    {
        $pathToComposerJson = sprintf('%s/composer.json', $path);
        if (!is_file($pathToComposerJson)) {
            return [];
        }

        $fileContent = file_get_contents($pathToComposerJson);
        if (!$fileContent) {
            return [];
        }

        return json_decode($fileContent, true);
    }

    /**
     * @param array $composerJsonAsArray
     *
     * @return string
     */
    protected function getOrganizationNameFromComposer(array $composerJsonAsArray): string
    {
        $nameFragments = explode('/', $composerJsonAsArray['name']);

        return $nameFragments[0];
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return array<mixed>
     */
    protected function getSrcPosition(SplFileInfo $directoryInfo): array
    {
        $directoryPath = $directoryInfo->getRealPath();
        $pathFragments = !$directoryPath ? [] : explode(DIRECTORY_SEPARATOR, $directoryPath);

        return [
            $pathFragments,
            array_search('src', $pathFragments),
        ];
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return string
     */
    protected function getOrganizationNameFromDirectory(SplFileInfo $directoryInfo): string
    {
        [$pathFragments, $srcPosition] = $this->getSrcPosition($directoryInfo);

        return $pathFragments[$srcPosition + 1];
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return string
     */
    protected function getApplicationNameFromDirectory(SplFileInfo $directoryInfo): string
    {
        [$pathFragments, $srcPosition] = $this->getSrcPosition($directoryInfo);

        return $pathFragments[$srcPosition + 2];
    }

    /**
     * @param array $composerJsonAsArray
     *
     * @return string
     */
    protected function getModuleNameFromComposer(array $composerJsonAsArray): string
    {
        $nameFragments = explode('/', $composerJsonAsArray['name']);

        return $nameFragments[1];
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return string
     */
    protected function getModuleNameFromDirectory(SplFileInfo $directoryInfo): string
    {
        return $directoryInfo->getRelativePathname();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function camelCase(string $value): string
    {
        $filterChain = new FilterChain();
        $filterChain->attach(new DashToCamelCase());

        return ucfirst($filterChain->filter($value));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function dasherize(string $value): string
    {
        $filterChain = new FilterChain();
        $filterChain
            ->attach(new CamelCaseToDash())
            ->attach(new StringToLower());

        return $filterChain->filter($value);
    }
}
