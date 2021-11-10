<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\SprykerLock;

use SprykerSdk\Integrator\IntegratorConfig;

class SprykerLockReader implements SprykerLockReaderInterface
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
     * @return array<string, array<string>>
     */
    public function getLockFileData(): array
    {
        $integratorLockFilePath = $this->config->getIntegratorLockFilePath();

        if (!file_exists($integratorLockFilePath)) {
            return [];
        }

        $json = file_get_contents($integratorLockFilePath);
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
