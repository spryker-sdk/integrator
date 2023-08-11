<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor\Module;

use SprykerSdk\Integrator\Composer\ComposerLockReaderInterface;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Executor\ManifestExecutorInterface;
use SprykerSdk\Integrator\Filter\ManifestsFiltersExecutorInterface;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface;
use SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface;
use SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

class ModuleManifestExecutor implements ModuleManifestExecutorInterface
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface
     */
    protected IntegratorLockWriterInterface $integratorLockWriter;

    /**
     * @var \SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface
     */
    protected RepositoryManifestReaderInterface $manifestReader;

    /**
     * @var \SprykerSdk\Integrator\Executor\ManifestExecutorInterface
     */
    protected ManifestExecutorInterface $manifestExecutor;

    /**
     * @var \SprykerSdk\Integrator\Composer\ComposerLockReaderInterface
     */
    protected $composerLockReader;

    /**
     * @var \SprykerSdk\Integrator\Filter\ManifestsFiltersExecutorInterface
     */
    protected ManifestsFiltersExecutorInterface $manifestsFiltersExecutor;

    /**
     * @var \SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface
     */
    private IntegratorLockReaderInterface $integratorLockReader;

    /**
     * @param \SprykerSdk\Integrator\IntegratorLock\IntegratorLockReaderInterface $integratorLockReader
     * @param \SprykerSdk\Integrator\IntegratorLock\IntegratorLockWriterInterface $integratorLockWriter
     * @param \SprykerSdk\Integrator\Manifest\RepositoryManifestReaderInterface $manifestReader
     * @param \SprykerSdk\Integrator\Executor\ManifestExecutorInterface $manifestExecutor
     * @param \SprykerSdk\Integrator\Composer\ComposerLockReaderInterface $composerLockReader
     * @param \SprykerSdk\Integrator\Filter\ManifestsFiltersExecutorInterface $manifestsFiltersExecutor
     */
    public function __construct(
        IntegratorLockReaderInterface $integratorLockReader,
        IntegratorLockWriterInterface $integratorLockWriter,
        RepositoryManifestReaderInterface $manifestReader,
        ManifestExecutorInterface $manifestExecutor,
        ComposerLockReaderInterface $composerLockReader,
        ManifestsFiltersExecutorInterface $manifestsFiltersExecutor
    ) {
        $this->integratorLockReader = $integratorLockReader;
        $this->manifestExecutor = $manifestExecutor;
        $this->integratorLockWriter = $integratorLockWriter;
        $this->manifestReader = $manifestReader;
        $this->composerLockReader = $composerLockReader;
        $this->manifestsFiltersExecutor = $manifestsFiltersExecutor;
    }

    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function runModuleManifestExecution(
        InputOutputInterface $inputOutput,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void {
        $lockedModules = $this->integratorLockReader->getLockFileData();
        $unappliedManifests = $this->manifestReader->readUnappliedManifests($commandArgumentsTransfer, $lockedModules);
        $unappliedManifests = $this->manifestsFiltersExecutor->filterManifests($unappliedManifests);
        $lockedModules = $this->manifestExecutor->applyManifestList($lockedModules, $unappliedManifests, $inputOutput, $commandArgumentsTransfer);

        if ($commandArgumentsTransfer->getIsDryOrFail()) {
            return;
        }

        $this->integratorLockWriter->storeLock($lockedModules);
    }

    /**
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $input
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function runUpdateLock(
        InputOutputInterface $input,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void {
        $moduleVersions = $this->composerLockReader->getModuleVersions();

        $this->integratorLockWriter->storeLock($moduleVersions);
        $input->write('<info>The integration lock file has been updated according to the project state.</info>', true);
    }
}
