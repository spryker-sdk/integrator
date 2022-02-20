<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Manifest;

use Composer\InstalledVersions;
use SprykerSdk\Integrator\Composer\ComposerLockReaderInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use ZipArchive;

class ManifestReader implements ManifestReaderInterface
{
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
     *
     * @return array<string, array<string, array<string>>>
     */
    public function readManifests(array $moduleTransfers): array
    {
        // Do not update repository folder when in local development
        if (!is_dir($this->config->getLocalRecipesDirectory())) {
            $this->updateRepositoryFolder();
        }

        $manifests = [];
        $moduleComposerData = $this->composerLockReader->getModuleVersions();

        foreach ($moduleTransfers as $moduleTransfer) {
            $moduleFullName = $moduleTransfer->getOrganization()->getName() . '.' . $moduleTransfer->getName();

            // Get the version from installed packages or the CLI passed one (e.g. Spryker.Acl:3.6.0).
            $version = $moduleComposerData[$moduleFullName] ?? $moduleTransfer->getVersion();

            if (!$version) {
                continue;
            }

            $filePath = $this->resolveManifestVersion($moduleTransfer, $version);

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
        $recipesArchive = $this->config->getRecipesDirectory() . 'archive.zip';

        if (!is_dir($this->config->getRecipesDirectory())) {
            mkdir($this->config->getRecipesDirectory(), 0700, true);
        }

        file_put_contents($recipesArchive, fopen($this->config->getRecipesRepository(), 'r'));

        $zip = new ZipArchive();
        $zip->open($recipesArchive);
        $zip->extractTo($this->config->getRecipesDirectory());
        $zip->close();
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     * @param string $moduleVersion
     *
     * @return string|null
     */
    protected function resolveManifestVersion(ModuleTransfer $moduleTransfer, string $moduleVersion)
    {
        $archiveDir = 'integrator-recipes-master/';
        $moduleRecipesDir = sprintf('%s%s%s/', $this->config->getRecipesDirectory(), $archiveDir, $moduleTransfer->getName());

        // When the recipes installed for local development use those instead of the one from the archive.
        if (is_dir($this->config->getLocalRecipesDirectory())) {
            $moduleRecipesDir = sprintf('%s%s/', $this->config->getLocalRecipesDirectory(), $moduleTransfer->getName());
        }

        // Check if module has any recipes
        if (!is_dir($moduleRecipesDir)) {
            return null;
        }

        // Recipe path with module name and expected version
        $filePath = $moduleRecipesDir . sprintf(
            '%s/installer-manifest.json',
            $moduleVersion,
        );

        if (file_exists($filePath)) {
            return $filePath;
        }

        $nextSuitableVersion = $this->findNextSuitableVersion($moduleRecipesDir, $moduleVersion);

        if (!$nextSuitableVersion) {
            return null;
        }

        return $moduleRecipesDir . sprintf(
            '%s/installer-manifest.json',
            $nextSuitableVersion,
        );
    }

    /**
     * @param string $moduleRecipesDir
     * @param string $moduleVersion
     *
     * @return string|null
     */
    protected function findNextSuitableVersion(string $moduleRecipesDir, string $moduleVersion): ?string
    {
        $versions = [];

        foreach ($this->getValidModuleVersions($moduleRecipesDir) as $version) {
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
     * @param string $moduleRecipesDir
     *
     * @return array<string>
     */
    protected function getValidModuleVersions(string $moduleRecipesDir): array
    {
        $validModuleVersions = [];
        $moduleVersionDirectories = scandir($moduleRecipesDir);
        $moduleVersionDirectories = !$moduleVersionDirectories ? [] : array_diff($moduleVersionDirectories, ['.', '..']);

        foreach ($moduleVersionDirectories as $moduleVersionDirectory) {
            if (!is_dir($moduleRecipesDir . $moduleVersionDirectory)) {
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
