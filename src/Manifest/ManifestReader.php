<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Manifest;

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
        file_put_contents(IntegratorConfig::getInstance()->getGlossaryFilePath(), 'readManifests', FILE_APPEND);
        $this->updateRepositoryFolder();
        $manifests = [];
        $moduleComposerData = $this->composerLockReader->getModuleVersions();

        foreach ($moduleTransfers as $moduleTransfer) {
            $moduleFullName = $moduleTransfer->getOrganizationOrFail()->getNameOrFail() . '.' . $moduleTransfer->getNameOrFail();
            file_put_contents(IntegratorConfig::getInstance()->getGlossaryFilePath(), '$moduleFullName: ' . $moduleFullName, FILE_APPEND);

            if (!isset($moduleComposerData[$moduleFullName])) {
                file_put_contents(IntegratorConfig::getInstance()->getGlossaryFilePath(), '!$moduleComposerData[$moduleFullName]: ', FILE_APPEND);
                continue;
            }

            $filePath = $this->resolveManifestVersion($moduleTransfer, $moduleComposerData[$moduleFullName]);

            if (!$filePath) {
                file_put_contents(IntegratorConfig::getInstance()->getGlossaryFilePath(), '!!$filePath : ', FILE_APPEND);
                continue;
            }
            file_put_contents(IntegratorConfig::getInstance()->getGlossaryFilePath(), '$filePath : ' . $filePath, FILE_APPEND);

            $json = file_get_contents($filePath);
            file_put_contents(IntegratorConfig::getInstance()->getGlossaryFilePath(), '$json : ' . $json, FILE_APPEND);
            if (!$json) {
                file_put_contents(IntegratorConfig::getInstance()->getGlossaryFilePath(), '!$json : ', FILE_APPEND);
                continue;
            }

            $manifest = json_decode($json, true);

            if ($manifest) {
                $manifests[$moduleFullName] = $manifest;
            }
        }
        file_put_contents(IntegratorConfig::getInstance()->getGlossaryFilePath(), '$manifests : ' . json_encode($manifests), FILE_APPEND);

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
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     * @param string $moduleVersion
     *
     * @return string|null
     */
    protected function resolveManifestVersion(ModuleTransfer $moduleTransfer, string $moduleVersion): ?string
    {
        $archiveDir = 'integrator-manifests-master/';
        $organization = $moduleTransfer->getOrganizationOrFail();
        $moduleRecipiesDir = sprintf('%s%s%s/%s/', $this->config->getManifestsDirectory(), $archiveDir, $organization->getName(), $moduleTransfer->getName());

        if (!is_dir($moduleRecipiesDir)) {
            return null;
        }

        $filePath = $moduleRecipiesDir . sprintf(
            '%s/installer-manifest.json',
            $moduleVersion,
        );

        if (file_exists($filePath)) {
            return $filePath;
        }

        $nextSuitableVersion = $this->findNextSuitableVersion($moduleRecipiesDir, $moduleVersion);

        if (!$nextSuitableVersion) {
            return null;
        }

        return $moduleRecipiesDir . sprintf(
            '%s/installer-manifest.json',
            $nextSuitableVersion,
        );
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
