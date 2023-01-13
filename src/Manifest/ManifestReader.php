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

class ManifestReader implements ManifestReaderInterface
{
    /**
     * @var string
     */
    protected const ARCHIVE_DIR = 'integrator-manifests-master/';

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
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return array<string, array<string, array<string>>>
     */
    public function readManifests(array $moduleTransfers, IntegratorCommandArgumentsTransfer $commandArgumentsTransfer): array
    {
        // Do not update repository folder when in local development
        if (!is_dir($this->config->getLocalRecipesDirectory()) && $commandArgumentsTransfer->getSource() === null) {
            $this->updateRepositoryFolder();
        }

        $manifests = [];
        $moduleComposerData = $this->composerLockReader->getModuleVersions();

        foreach ($moduleTransfers as $moduleTransfer) {
            $moduleFullName = $moduleTransfer->getOrganizationOrFail()->getNameOrFail() . '.' . $moduleTransfer->getNameOrFail();

            // Get the version from installed packages or the CLI passed one (e.g. Spryker.Acl:3.6.0).
            $version = $moduleComposerData[$moduleFullName] ?? $moduleTransfer->getVersion();

            if (!$version) {
                continue;
            }

            $moduleManifestsDir = $this->getModuleManifestsDir($moduleTransfer, $commandArgumentsTransfer);

            if (!is_dir($moduleManifestsDir)) {
                continue;
            }

            $filePath = $this->resolveManifestVersion($moduleManifestsDir, $version);

            if (!$filePath) {
                continue;
            }

            $json = file_get_contents($filePath);
            if (!$json) {
                continue;
            }

            $manifest = json_decode($json, true);

            if ($manifest) {
                $manifests[$moduleFullName] = $manifest;
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
     * @return string|null
     */
    protected function resolveManifestVersion(string $moduleManifestsDir, string $moduleVersion): ?string
    {
        // Recipe path with module name and expected version
        $filePath = $moduleManifestsDir . sprintf(
            '%s/installer-manifest.json',
            $moduleVersion,
        );

        if (file_exists($filePath)) {
            return $filePath;
        }

        $nextSuitableVersion = $this->findNextSuitableVersion($moduleManifestsDir, $moduleVersion);

        if (!$nextSuitableVersion) {
            return null;
        }

        return $moduleManifestsDir . sprintf(
            '%s/installer-manifest.json',
            $nextSuitableVersion,
        );
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return string
     */
    protected function getModuleManifestsDir(
        ModuleTransfer $moduleTransfer,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): string {
        $organization = $moduleTransfer->getOrganizationOrFail();

        if ($commandArgumentsTransfer->getSource() !== null) {
            return sprintf(
                '%s/%s/%s/',
                $commandArgumentsTransfer->getSource(),
                $organization->getName(),
                $moduleTransfer->getName(),
            );
        }

        $moduleManifestsDir = sprintf(
            '%s%s%s/%s/',
            $this->config->getManifestsDirectory(),
            static::ARCHIVE_DIR,
            $organization->getName(),
            $moduleTransfer->getName(),
        );

        // When the recipes installed for local development use those instead of the one from the archive.
        if (is_dir($this->config->getLocalRecipesDirectory())) {
            $moduleManifestsDir = sprintf('%s%s/', $this->config->getLocalRecipesDirectory(), $moduleTransfer->getName());
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
