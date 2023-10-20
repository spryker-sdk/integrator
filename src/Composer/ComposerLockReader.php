<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Composer;

use SprykerSdk\Integrator\Common\UtilText\TextCaseHelper;
use SprykerSdk\Integrator\IntegratorConfig;

class ComposerLockReader implements ComposerLockReaderInterface
{
    /**
     * @var string
     */
    protected const PREFIX_DEV_PACKAGE = 'dev-';

    /**
     * @var string
     */
    protected const PREFIX_ORGANIZATION = 'Spryker';

    /**
     * @var string
     */
    protected const PREFIX_FEATURE_ORGANIZATION = 'SprykerFeature';

    /**
     * @var array<string>
     */
    protected const GROUP_PACKAGES = ['packages', 'packages-dev'];

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     */
    public function __construct(IntegratorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return array<string>
     */
    public function getModuleVersions(): array
    {
        $composerLockData = $this->getProjectComposerLockData();
        $packages = [];

        foreach (static::GROUP_PACKAGES as $packagesKey) {
            if (!isset($composerLockData[$packagesKey])) {
                continue;
            }
            /** @var array $packageData */
            foreach ($composerLockData[$packagesKey] as $packageData) {
                if (strpos($packageData['version'], static::PREFIX_DEV_PACKAGE) === 0) {
                    continue;
                }
                [$packageName, $aliasedVersion] = $this->getPackageVersion($packageData);
                if (
                    stripos($packageName, static::PREFIX_ORGANIZATION) === false ||
                    stripos($packageName, static::PREFIX_FEATURE_ORGANIZATION) !== false
                ) {
                    continue;
                }
                $packages[$packageName] = $aliasedVersion;
            }
        }

        return $packages;
    }

    /**
     * @param string $packageName
     *
     * @return array<string>|null
     */
    public function getPackageData(string $packageName): ?array
    {
        $composerLockData = $this->getProjectComposerLockData();

        foreach (static::GROUP_PACKAGES as $packagesKey) {
            if (!isset($composerLockData[$packagesKey])) {
                continue;
            }

            foreach ($composerLockData[$packagesKey] as $packageData) {
                if ($packageData['name'] === $packageName) {
                    return $packageData;
                }
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $packageData
     *
     * @return array<int, string>
     */
    protected function getPackageVersion(array $packageData): array
    {
        [$org, $module] = explode('/', $packageData['name']);
        $packageName = sprintf('%s.%s', TextCaseHelper::dashToCamelCase($org), TextCaseHelper::dashToCamelCase($module));

        if (strpos($packageData['version'], 'dev-') !== false) {
            $versionFromExtra = $packageData['extra']['branch-alias']['dev-master'] ?? false;
            if ($versionFromExtra) {
                $aliasedVersion = str_replace('x-dev', '0', $versionFromExtra);

                return [$packageName, $aliasedVersion];
            }
        }

        return [$packageName, $packageData['version']];
    }

    /**
     * @return array<array<array<string>>>
     */
    protected function getProjectComposerLockData(): array
    {
        $composerLockFilePath = $this->config->getComposerLockFilePath();

        if (!file_exists($composerLockFilePath)) {
            return [];
        }

        $json = file_get_contents($composerLockFilePath);
        if (!$json) {
            return [];
        }

        $lockData = json_decode($json, true);

        if (json_last_error()) {
            return [];
        }

        return $lockData;
    }
}
