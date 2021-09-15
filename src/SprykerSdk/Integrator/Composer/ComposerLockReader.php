<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Composer;

use SprykerSdk\Integrator\Common\UtilText\Filter\SeparatorToCamelCase;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Shared\Common\UtilText\Filter\SeparatorToCamelCase;

class ComposerLockReader
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
     * @return string[]
     */
    public function getModuleVersions(): array
    {
        $composerLockData = $this->getProjectComposerLockData();
        if (!isset($composerLockData['packages'])) {
            return [];
        }

        $packages = [];

        $dashToCamelCaseFilter = new SeparatorToCamelCase();
        foreach ($composerLockData['packages'] as $packageData) {
            if ($packageData['version'] === 'dev-master') {
                continue;
            }
            [$org, $module] = explode('/', $packageData['name']);
            $packageName = $dashToCamelCaseFilter->filter($org, '-', true) . '.' . $dashToCamelCaseFilter->filter($module, '-', true);
            $packages[$packageName] = $packageData['version'];
        }

        return $packages;
    }

    /**
     * @return string[][][]
     */
    protected function getProjectComposerLockData(): array
    {
        $composerLockFilePath = $this->config->getComposerLockFilePath();

        if (!file_exists($composerLockFilePath)) {
            return [];
        }

        $lockData = json_decode(file_get_contents($composerLockFilePath), true);

        if (json_last_error()) {
            return [];
        }

        return $lockData;
    }
}
