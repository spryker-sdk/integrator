<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Manifest;

use SprykerSdk\Integrator\Composer\ComposerLockReaderInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use ZipArchive;

class RepositoryRepositoryManifestReader implements RepositoryManifestReaderInterface
{
    /**
     * @var string
     */
    public const ARCHIVE_DIR = 'integrator-manifests-master/';

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @var \SprykerSdk\Integrator\Composer\ComposerLockReaderInterface
     */
    protected $composerLockReader;

    /**
     * @param \SprykerSdk\Integrator\Composer\ComposerLockReaderInterface $composerLockReader
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     */
    public function __construct(ComposerLockReaderInterface $composerLockReader, IntegratorConfig $config)
    {
        $this->config = $config;
        $this->composerLockReader = $composerLockReader;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return array<string, array<string, array<string>>>
     */
    public function readManifests(IntegratorCommandArgumentsTransfer $commandArgumentsTransfer): array
    {
        // Do not update repository folder when in local development
        if (!is_dir($this->config->getLocalRecipesDirectory()) && $commandArgumentsTransfer->getSource() === null) {
            $this->updateRepositoryFolder();
        }

        $manifests = [];
        $moduleComposerData = $this->composerLockReader->getModuleVersions();
        $filterModules = array_map(
            function (ModuleTransfer $moduleTransfer): string {
                return sprintf('%s.%s', $moduleTransfer->getOrganization(), $moduleTransfer->getModule());
            },
            $commandArgumentsTransfer->getModules(),
        );
        foreach ($moduleComposerData as $moduleFullName => $version) {
            if ($filterModules && !in_array($moduleFullName, $filterModules)) {
                continue;
            }
            $moduleManifestsDir = $this->getModuleManifestsDir($moduleFullName, $commandArgumentsTransfer);

            if (!is_dir($moduleManifestsDir)) {
                continue;
            }

            [$version, $filePath] = $this->resolveManifestVersion($moduleManifestsDir, $version);

            if (!$filePath) {
                continue;
            }

            $json = file_get_contents($filePath);
            if (!$json) {
                continue;
            }

            $manifestFileData = json_decode($json, true);
            if ($manifestFileData) {
                foreach ($manifestFileData as &$strategy) {
                    foreach ($strategy as &$manifest) {
                        $manifest[IntegratorConfig::MODULE_KEY] = $moduleFullName;
                        $manifest[IntegratorConfig::MODULE_VERSION_KEY] = $version;
                    }
                }
                unset($strategy, $manifest);
                $manifests[$moduleFullName] = $manifestFileData;
            }
        }

        return $manifests;
    }

    /**
     * @return void
     */
    protected function updateRepositoryFolder(): void
    {
        $manifestsArchive = $this->config->getManifestsDirectory() . 'archive.zip';

        if (!is_dir($this->config->getManifestsDirectory())) {
            mkdir($this->config->getManifestsDirectory(), 0700, true);
        }

        file_put_contents($manifestsArchive, fopen($this->config->getManifestsRepository(), 'r'));

        $zip = new ZipArchive();
        $zip->open($manifestsArchive);
        $zip->extractTo($this->config->getManifestsDirectory());
        $zip->close();
    }

    /**
     * @param string $moduleManifestsDir
     * @param string $moduleVersion
     *
     * @return array<string|null>
     */
    protected function resolveManifestVersion(string $moduleManifestsDir, string $moduleVersion): array
    {
        // Recipe path with module name and expected version
        $filePath = $moduleManifestsDir . sprintf(
            '%s/installer-manifest.json',
            $moduleVersion,
        );

        if (file_exists($filePath)) {
            return [$moduleVersion, $filePath];
        }

        $nextSuitableVersion = $this->findNextSuitableVersion($moduleManifestsDir, $moduleVersion);

        if (!$nextSuitableVersion) {
            return [$moduleVersion, null];
        }

        return [
            $nextSuitableVersion,
            $moduleManifestsDir . sprintf(
                '%s/installer-manifest.json',
                $nextSuitableVersion,
            ),
        ];
    }

    /**
     * @param string $moduleFullName
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return string
     */
    protected function getModuleManifestsDir(
        string $moduleFullName,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): string {
        [$organization, $moduleName] = explode('.', $moduleFullName);

        if ($commandArgumentsTransfer->getSource() !== null) {
            return sprintf(
                '%s/%s/%s/',
                $commandArgumentsTransfer->getSource(),
                $organization,
                $moduleName,
            );
        }

        $moduleManifestsDir = sprintf(
            '%s%s%s/%s/',
            $this->config->getManifestsDirectory(),
            static::ARCHIVE_DIR,
            $organization,
            $moduleName,
        );

        // When the recipes installed for local development use those instead of the one from the archive.
        if (is_dir($this->config->getLocalRecipesDirectory())) {
            $moduleManifestsDir = sprintf('%s%s/', $this->config->getLocalRecipesDirectory(), $moduleName);
        }

        return $moduleManifestsDir;
    }

    /**
     * @param string $moduleManifestsDir
     * @param string $moduleVersion
     *
     * @return string|null
     */
    protected function findNextSuitableVersion(string $moduleManifestsDir, string $moduleVersion): ?string
    {
        $versions = [];

        foreach ($this->getValidModuleVersions($moduleManifestsDir) as $version) {
            if (version_compare($version, $moduleVersion, 'gt')) {
                continue;
            }

            $versions[] = $version;
        }

        if (!$versions) {
            return null;
        }

        $versions = $this->sortArray($versions);
        $end = end($versions);

        return $end ?: null;
    }

    /**
     * @param string $moduleManifestsDir
     *
     * @return array<string>
     */
    protected function getValidModuleVersions(string $moduleManifestsDir): array
    {
        $validModuleVersions = [];
        $moduleVersionDirectories = scandir($moduleManifestsDir);
        $moduleVersionDirectories = !$moduleVersionDirectories ? [] : array_diff($moduleVersionDirectories, ['.', '..']);

        foreach ($moduleVersionDirectories as $moduleVersionDirectory) {
            if (!is_dir($moduleManifestsDir . $moduleVersionDirectory)) {
                continue;
            }

            $validModuleVersions[] = $moduleVersionDirectory;
        }

        return $validModuleVersions;
    }

    /**
     * @param array<string> $versions
     *
     * @return array<string>
     */
    protected function sortArray(array $versions): array
    {
        if (count($versions) === 0) {
            return [];
        }

        $pivotArray = array_splice(
            $versions,
            (int)floor((count($versions) - 1) / 2),
            1,
        );

        $smaller = [];
        $greater = [];

        foreach ($versions as $version) {
            if (version_compare($version, $pivotArray[0], 'gt')) {
                $greater[] = $version;
            } else {
                $smaller[] = $version;
            }
        }

        return array_merge(
            $this->sortArray($smaller),
            $pivotArray,
            $this->sortArray($greater),
        );
    }
}
