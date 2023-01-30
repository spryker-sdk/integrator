<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor\Module;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Executor\ManifestExecutorInterface;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface;
use SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

class ModuleManifestExecutor implements ModuleManifestExecutorInterface
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface
     */
    protected $integratorLockReader;

    /**
     * @var \SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface
     */
    protected $integratorLockWriter;

    /**
     * @var \SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface
     */
    protected $manifestReader;

    /**
     * @var \SprykerSdk\Integrator\Executor\ManifestExecutorInterface
     */
    protected ManifestExecutorInterface $manifestExecutor;

    /**
     * @param \SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface $integratorLockReader
     * @param \SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface $integratorLockWriter
     * @param \SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface $manifestReader
     * @param \SprykerSdk\Integrator\Executor\ManifestExecutorInterface $manifestExecutor
     */
    public function __construct(
        IntegratorLockReaderInterface $integratorLockReader,
        IntegratorLockWriterInterface $integratorLockWriter,
        RepositoryManifestReaderInterface $manifestReader,
        ManifestExecutorInterface $manifestExecutor
    ) {
        $this->manifestExecutor = $manifestExecutor;
        $this->integratorLockWriter = $integratorLockWriter;
        $this->integratorLockReader = $integratorLockReader;
        $this->manifestReader = $manifestReader;
    }

    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return int
     */
    public function runModuleManifestExecution(
        array $moduleTransfers,
        InputOutputInterface $inputOutput,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): int {
        $this->assertModuleData($moduleTransfers);

        $manifests = $this->manifestReader->readManifests($moduleTransfers, $commandArgumentsTransfer);

        $lockedModules = $this->integratorLockReader->getLockFileData();

        $unappliedManifests = $this->manifestExecutor->findUnappliedManifests($manifests, $lockedModules);

        $this->manifestExecutor->applyManifestList($unappliedManifests, $inputOutput, $commandArgumentsTransfer);

        if ($commandArgumentsTransfer->getIsDryOrFail()) {
            return 0;
        }

        return $this->integratorLockWriter->storeLock($lockedModules);
    }

    /**
     * @param array<\SprykerSdk\Integrator\Transfer\ModuleTransfer> $moduleTransfers
     *
     * @return void
     */
    protected function assertModuleData(array $moduleTransfers): void
    {
        foreach ($moduleTransfers as $moduleTransfer) {
            $moduleTransfer->requireName()
                ->requireNameDashed()
                ->requireOrganization();
            $moduleTransfer->getOrganizationOrFail()
                ->requireNameDashed()
                ->requireName();
        }
    }
}
