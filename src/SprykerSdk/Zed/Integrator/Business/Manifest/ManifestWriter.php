<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Zed\Integrator\Business\Manifest;

class ManifestWriter
{
    /**
     * @param \Generated\Shared\Transfer\ModuleTransfer[] $moduleTransfers
     * @param array $manifests
     *
     * @return bool
     */
    public function storeManifest(array $moduleTransfers, array $manifests): bool
    {
        $success = true;
        foreach ($manifests as $moduleKey => $manifest) {
            $moduleName = explode('.', $moduleKey)[1];
            if (!isset($moduleTransfers[$moduleName])) {
                continue;
            }
            $moduleTransfer = $moduleTransfers[$moduleName];

            $lockFilePath = $moduleTransfer->getPath() . 'installer-manifest.json';
            $json = json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;
            if (!file_put_contents($lockFilePath, $json) === false) {
                $success = false;
            }
        }

        return $success;
    }
}
