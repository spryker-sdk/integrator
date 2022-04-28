<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder\Module\ProjectModuleFinder;

use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcherInterface;
use SprykerSdk\Integrator\Transfer\ApplicationTransfer;
use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use SprykerSdk\Integrator\Transfer\OrganizationTransfer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ProjectModuleFinder implements ProjectModuleFinderInterface
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
    public function getProjectModules(?ModuleFilterTransfer $moduleFilterTransfer = null): array
    {
        $moduleCollection = [];

        $projectDirectories = $this->getProjectDirectories();

        if (count($projectDirectories) === 0) {
            return $moduleCollection;
        }

        foreach ($this->getProjectModuleFinder($projectDirectories) as $directoryInfo) {
            $moduleTransfer = $this->getModuleTransfer($directoryInfo);
            if (isset($moduleCollection[$this->buildOrganizationModuleKey($moduleTransfer)])) {
                $moduleTransfer = $moduleCollection[$this->buildOrganizationModuleKey($moduleTransfer)];
            }

            $applicationTransfer = $this->buildApplicationTransferFromDirectoryInformation($directoryInfo);
            $moduleTransfer->addApplication($applicationTransfer);

            if ($moduleFilterTransfer !== null && !$this->moduleMatcher->matches($moduleTransfer, $moduleFilterTransfer)) {
                continue;
            }

            $moduleCollection[$this->buildOrganizationModuleKey($moduleTransfer)] = $moduleTransfer;
        }

        ksort($moduleCollection);

        return $moduleCollection;
    }

    /**
     * @return array
     */
    protected function getProjectDirectories(): array
    {
        $projectOrganizationModuleDirectories = [];
        $applicationSourceDir = $this->config->getApplicationSourceDir();
        foreach ($this->config->getApplications() as $application) {
            $projectOrganizationModuleDirectories[] = sprintf('%s/*/%s/', $applicationSourceDir, $application);
        }

        return array_filter($projectOrganizationModuleDirectories, 'glob');
    }

    /**
     * @param array $projectOrganizationModuleDirectories
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function getProjectModuleFinder(array $projectOrganizationModuleDirectories): Finder
    {
        /** @var \Closure $callback */
        $callback = $this->getFilenameSortCallback();

        return (new Finder())
            ->directories()
            ->depth('== 0')
            ->in($projectOrganizationModuleDirectories)
            ->sort($callback);
    }

    /**
     * @return callable
     */
    protected function getFilenameSortCallback(): callable
    {
        return function (SplFileInfo $fileOne, SplFileInfo $fileTwo) {
            $fileOnePath = $fileOne->getRealpath();
            $fileTwoPath = $fileTwo->getRealpath();
            if (!$fileOnePath || !$fileTwoPath) {
                return 0;
            }

            return strcmp($fileOnePath, $fileTwoPath);
        };
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function getModuleTransfer(SplFileInfo $directoryInfo): ModuleTransfer
    {
        $moduleTransfer = $this->buildModuleTransferFromDirectoryInformation($directoryInfo);
        $moduleTransfer->setOrganization($this->buildOrganizationTransferFromDirectoryInformation($directoryInfo));

        return $moduleTransfer;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function buildModuleTransferFromDirectoryInformation(SplFileInfo $directoryInfo): ModuleTransfer
    {
        $moduleName = $this->getModuleNameFromDirectory($directoryInfo);
        $moduleTransfer = new ModuleTransfer();
        $moduleTransfer
            ->setName($moduleName)
            ->setPath(dirname($this->config->getApplicationSourceDir()) . DIRECTORY_SEPARATOR);

        return $moduleTransfer;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\OrganizationTransfer
     */
    protected function buildOrganizationTransferFromDirectoryInformation(SplFileInfo $directoryInfo): OrganizationTransfer
    {
        $organizationName = $this->getOrganizationNameFromDirectory($directoryInfo);
        $organizationTransfer = new OrganizationTransfer();
        $organizationTransfer
            ->setName($organizationName)
            ->setIsProject(true);

        return $organizationTransfer;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\ApplicationTransfer
     */
    protected function buildApplicationTransferFromDirectoryInformation(SplFileInfo $directoryInfo): ApplicationTransfer
    {
        $applicationName = $this->getApplicationNameFromDirectory($directoryInfo);
        $applicationTransfer = new ApplicationTransfer();
        $applicationTransfer->setName($applicationName);

        return $applicationTransfer;
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
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return string
     */
    protected function getModuleNameFromDirectory(SplFileInfo $directoryInfo): string
    {
        return $directoryInfo->getRelativePathname();
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return string
     */
    protected function buildOrganizationModuleKey(ModuleTransfer $moduleTransfer): string
    {
        return sprintf('%s.%s', $moduleTransfer->getOrganizationOrFail()->getNameOrFail(), $moduleTransfer->getNameOrFail());
    }
}
