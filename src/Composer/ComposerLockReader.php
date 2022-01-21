<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Composer;

use SprykerSdk\Integrator\Common\UtilText\Filter\SeparatorToCamelCase;
use SprykerSdk\Integrator\IntegratorConfig;

class ComposerLockReader implements ComposerLockReaderInterface
{
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
        if (!isset($composerLockData['packages'])) {
            return [];
        }

        $packages = [];

        $dashToCamelCaseFilter = new SeparatorToCamelCase();

        /** @var array $packageData */
        foreach ($composerLockData['packages'] as $packageData) {
            if ($packageData['version'] === 'dev-master') {
                continue;
            }
            [$org, $module] = explode('/', $packageData['name']);
            $packageName = $dashToCamelCaseFilter->filter($org, '-', true) . '.' . $dashToCamelCaseFilter->filter($module, '-', true);
            $packages[$packageName] = $packageData['version'];

            if (strpos($packageData['version'], 'dev-') !== false) {
                $versionFromExtra = $packageData['extra']['branch-alias']['dev-master'] ?? false;
                if ($versionFromExtra) {
                    $aliasedVersion = str_replace('x-dev', '0', $versionFromExtra);
                    $packages[$packageName] = $aliasedVersion;
                }
            }
        }

        return $packages;
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
