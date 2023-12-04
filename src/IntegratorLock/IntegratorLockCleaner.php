<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\IntegratorLock;

use SprykerSdk\Integrator\Executor\ProcessExecutor;
use SprykerSdk\Integrator\IntegratorConfig;

class IntegratorLockCleaner implements IntegratorLockCleanerInterface
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected IntegratorConfig $config;

    /**
     * @var \SprykerSdk\Integrator\Executor\ProcessExecutor
     */
    protected ProcessExecutor $processExecutor;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \SprykerSdk\Integrator\Executor\ProcessExecutor $processExecutor
     */
    public function __construct(IntegratorConfig $config, ProcessExecutor $processExecutor)
    {
        $this->config = $config;
        $this->processExecutor = $processExecutor;
    }

    /**
     * @return void
     */
    public function deleteLock(): void
    {
        $lockFilePath = $this->config->getIntegratorLockFilePath();

        unlink($lockFilePath);

        if (!$this->isLockFileChangeTrackedByGit($lockFilePath)) {
            return;
        }

        $this->processExecutor->execute(['git', 'add', $lockFilePath]);
        $this->processExecutor->execute(['git', 'commit', '-m', 'Removed `integrator.lock` file.']);
    }

    /**
     * @param string $filepath
     *
     * @return bool
     */
    protected function isLockFileChangeTrackedByGit(string $filepath): bool
    {
        $process = $this->processExecutor->execute(['git', 'status', '--porcelain', $filepath]);

        return $process->getOutput() !== '';
    }
}
