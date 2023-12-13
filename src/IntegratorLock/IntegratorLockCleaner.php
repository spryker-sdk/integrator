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
     * @var string
     */
    protected const GITIGNORE_FILE = '.gitignore';

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

        if ($this->isLockFileIgnoredByGit($lockFilePath)) {
            return;
        }
        if (file_exists($lockFilePath)) {
            unlink($lockFilePath);
        }
        $gitignorePath = $this->config->getProjectRootDirectory() . static::GITIGNORE_FILE;

        if (strpos((string)file_get_contents($gitignorePath), $this->config::INTEGRATOR_LOCK) === false) {
            file_put_contents($gitignorePath, $this->config::INTEGRATOR_LOCK . PHP_EOL, FILE_APPEND);
        }

        $this->processExecutor->execute(['git', 'add', $lockFilePath, $gitignorePath]);
        $this->processExecutor->execute(['git', 'commit', '-m', sprintf('Removed `%s` file.', $this->config::INTEGRATOR_LOCK), '-n']);
    }

    /**
     * @param string $filepath
     *
     * @return bool
     */
    protected function isLockFileIgnoredByGit(string $filepath): bool
    {
        $process = $this->processExecutor->execute(['git', 'check-ignore', $filepath]);

        return (bool)$process->getOutput();
    }
}
