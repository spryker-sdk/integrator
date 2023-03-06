<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\IntegratorLock;

use SprykerSdk\Integrator\IntegratorConfig;

class IntegratorLockWriter implements IntegratorLockWriterInterface
{
    /**
     * @var string
     */
    protected const REPLACE_4_WITH_2_SPACES = '/^(  +?)\\1(?=[^ ])/m';

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
     * @param array $lockData
     *
     * @return int
     */
    public function storeLock(array $lockData): int
    {
        $lockFilePath = $this->config->getIntegratorLockFilePath();

        $json = json_encode($lockData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if (!$json) {
            return 0;
        }

        $json = preg_replace(static::REPLACE_4_WITH_2_SPACES, '$1', $json) . PHP_EOL;
        if (file_put_contents($lockFilePath, $json) === false) {
            return 1;
        }

        return 0;
    }
}
