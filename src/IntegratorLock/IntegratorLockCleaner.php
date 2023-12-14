<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\IntegratorLock;

use SprykerSdk\Integrator\Executor\ProcessExecutor;
use SprykerSdk\Integrator\IntegratorConfig;
use Symfony\Component\Filesystem\Filesystem;

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
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \SprykerSdk\Integrator\Executor\ProcessExecutor $processExecutor
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(IntegratorConfig $config, ProcessExecutor $processExecutor, Filesystem $filesystem)
    {
        $this->config = $config;
        $this->processExecutor = $processExecutor;
        $this->filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public function deleteLock(): void
    {
        $lockFilePath = $this->config->getIntegratorLockFilePath();

        if ($this->isLockFileIgnoredByGit($lockFilePath) && !$this->filesystem->exists($lockFilePath)) {
            return;
        }
        if ($this->filesystem->exists($lockFilePath)) {
            $this->filesystem->remove($lockFilePath);
        }
        $gitignorePath = $this->config->getProjectRootDirectory() . static::GITIGNORE_FILE;

        if (!$this->filesystem->exists($gitignorePath) || strpos((string)file_get_contents($gitignorePath), $this->config::INTEGRATOR_LOCK) === false) {
            $this->filesystem->appendToFile($gitignorePath, PHP_EOL . $this->config::INTEGRATOR_LOCK . PHP_EOL);
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
