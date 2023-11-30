<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\IntegratorLock;

use SprykerSdk\Integrator\IntegratorConfig;
use Throwable;

class IntegratorLockCleaner implements IntegratorLockCleanerInterface
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
     * @return int
     */
    public function deleteLock(): int
    {
        $lockFilePath = $this->config->getIntegratorLockFilePath();

        return unlink($lockFilePath) === false ? 1 : 0;
    }
}
