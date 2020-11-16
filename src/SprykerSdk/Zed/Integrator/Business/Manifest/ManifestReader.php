<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business\Manifest;

use Generated\Shared\Transfer\ModuleTransfer;
use SprykerSdk\Zed\Integrator\IntegratorConfig;

class ManifestReader
{
    /**
     * @var \SprykerSdk\Zed\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @param \SprykerSdk\Zed\Integrator\IntegratorConfig $config
     */
    public function __construct(IntegratorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\ModuleTransfer[] $moduleTransfers
     *
     * @return string[][][]
     */
    public function readManifests(array $moduleTransfers): array
    {
        $manifests = [];
        foreach ($moduleTransfers as $moduleTransfer) {
            $filePath = $this->getManifestFilePath($moduleTransfer);

            if (!file_exists($filePath)) {
                continue;
            }

            $manifest = json_decode(file_get_contents($filePath), true);

            if ($manifest) {
                $manifests[$moduleTransfer->getOrganization()->getName() . '.' . $moduleTransfer->getName()] = $manifest;
            }
        }

        return $manifests;
    }

    /**
     * @param \Generated\Shared\Transfer\ModuleTransfer $moduleTransfer
     *
     * @return string
     */
    protected function getManifestFilePath(ModuleTransfer $moduleTransfer): string
    {
        return APPLICATION_ROOT_DIR . sprintf(
            '/vendor/spryker-sdk/integrator/data/%s/installer-manifest.json',
            $moduleTransfer->getName()
        );
    }
}
