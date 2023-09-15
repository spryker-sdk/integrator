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
    public function __construct(
        ComposerLockReaderInterface $composerLockReader,
        IntegratorConfig $config
    ) {
        $this->composerLockReader = $composerLockReader;
        $this->config = $config;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     * @param array<string, string> $lockedModules
     *
     * @return array<string, array<string, array<string>>>
     */
    public function readUnappliedManifests(IntegratorCommandArgumentsTransfer $commandArgumentsTransfer, array $lockedModules): array
    {
        // Do not update repository folder when in local development
        if (!is_dir($this->config->getLocalRecipesDirectory()) && $commandArgumentsTransfer->getSource() === null) {
            $this->updateRepositoryFolder();
        }

        $manifests = [];
        $moduleComposerData = $this->composerLockReader->getModuleVersions();

        $filterModules = $this->getFilterModules($commandArgumentsTransfer);

        foreach ($moduleComposerData as $moduleFullName => $currentVersion) {
            $fromVersion = $lockedModules[$moduleFullName] ?? '0.0.0';

            if ($this->shouldBeSkipped($filterModules, $moduleFullName, $fromVersion, $currentVersion)) {
                continue;
            }

            $moduleManifestsDir = $this->getModuleManifestsDir($moduleFullName, $commandArgumentsTransfer);

            if (!is_dir($moduleManifestsDir)) {
                continue;
            }

            $versions = isset($filterModules[$moduleFullName])
                ? [$filterModules[$moduleFullName]]
                : $this->resolveManifestVersions($moduleManifestsDir, $fromVersion, $currentVersion);

            foreach ($versions as $version) {
                $manifests = $this->appendManifests($moduleManifestsDir, $moduleFullName, $version, $manifests);
            }
        }

        return $manifests;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return array<string, null|string>
     */
    public function getFilterModules(IntegratorCommandArgumentsTransfer $commandArgumentsTransfer): array
    {
        $filterModules = [];

        foreach ($commandArgumentsTransfer->getModules() as $module) {
            $filterModules[sprintf('%s.%s', $module->getOrganization(), $module->getModule())] = $module->getVersion();
        }

        return $filterModules;
    }

    /**
     * @param array<string, null|string> $filterModules
     * @param string $moduleFullName
     * @param string $fromVersion
     * @param string $currentVersion
     *
     * @return bool
     */
    protected function shouldBeSkipped(array $filterModules, string $moduleFullName, string $fromVersion, string $currentVersion): bool
    {
        if (!$filterModules) {
            return version_compare($fromVersion, $currentVersion, '>=');
        }

        if (!array_key_exists($moduleFullName, $filterModules)) {
            return true;
        }

        return $filterModules[$moduleFullName] === null && version_compare($fromVersion, $currentVersion, '>=');
    }

    /**
     * @param string $moduleManifestsDir
     * @param string $moduleFullName
     * @param string $currentVersion
     * @param array $manifests
     *
     * @return array
     */
    protected function appendManifests(string $moduleManifestsDir, string $moduleFullName, string $currentVersion, array $manifests): array
    {
        $manifestFile = $moduleManifestsDir . sprintf('%s/installer-manifest.json', $currentVersion);

        if (!is_file($manifestFile)) {
            return $manifests;
        }

        $json = file_get_contents($manifestFile);

        if (!$json) {
            return $manifests;
        }

        $manifestFileData = json_decode($json, true);

        if ($manifestFileData) {
            if (!isset($manifests[$moduleFullName])) {
                $manifests[$moduleFullName] = [];
            }
            foreach ($manifestFileData as $strategy => $strategyManifests) {
                if (!isset($manifests[$moduleFullName][$strategy])) {
                    $manifests[$moduleFullName][$strategy] = [];
                }
                foreach ($strategyManifests as $manifest) {
                    $manifest[IntegratorConfig::MODULE_KEY] = $moduleFullName;
                    $manifest[IntegratorConfig::MODULE_VERSION_KEY] = $currentVersion;
                    $manifests[$moduleFullName][$strategy][] = $manifest;
                }
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
     * @param string $fromVersion
     * @param string $moduleVersion
     *
     * @return array<string>
     */
    protected function resolveManifestVersions(string $moduleManifestsDir, string $fromVersion, string $moduleVersion): array
    {
        $versions = [];

        foreach ($this->getValidModuleVersions($moduleManifestsDir) as $version) {
            if (
                version_compare($version, $fromVersion, '<=') ||
                version_compare($version, $moduleVersion, '>')
            ) {
                continue;
            }

            $versions[] = $version;
        }
        usort($versions, fn ($v1, $v2) => version_compare($v1, $v2));

        return $versions;
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
}
