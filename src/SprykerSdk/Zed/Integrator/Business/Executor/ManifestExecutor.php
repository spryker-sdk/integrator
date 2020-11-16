<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business\Executor;

use RuntimeException;
use SprykerSdk\Zed\Integrator\Business\Manifest\ManifestReader;
use SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ManifestStrategyInterface;
use SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockReader;
use SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockWriter;
use SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface;

class ManifestExecutor
{
    /**
     * @var \SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockReader
     */
    protected $sprykerLockReader;

    /**
     * @var \SprykerSdk\Zed\Integrator\Business\Manifest\ManifestReader
     */
    protected $manifestReader;

    /**
     * @var \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ManifestStrategyInterface[]
     */
    protected $manifestExecutors;

    /**
     * @var \SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockWriter
     */
    protected $sprykerLockWriter;

    /**
     * @param \SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockReader $sprykerLockReader
     * @param \SprykerSdk\Zed\Integrator\Business\Manifest\ManifestReader $manifestReader
     * @param \SprykerSdk\Zed\Integrator\Business\SprykerLock\SprykerLockWriter $sprykerLockWriter
     * @param \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ManifestStrategyInterface[] $manifestExecutors
     */
    public function __construct(
        SprykerLockReader $sprykerLockReader,
        ManifestReader $manifestReader,
        SprykerLockWriter $sprykerLockWriter,
        array $manifestExecutors
    ) {
        $this->sprykerLockReader = $sprykerLockReader;
        $this->manifestReader = $manifestReader;
        $this->manifestExecutors = $manifestExecutors;
        $this->sprykerLockWriter = $sprykerLockWriter;
    }

    /**
     * @param \Generated\Shared\Transfer\ModuleTransfer[] $moduleTransfers
     * @param \SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface $inputOutput
     * @param bool $isDry
     *
     * @return int
     */
    public function runModuleManifestExecution(array $moduleTransfers, IOInterface $inputOutput, bool $isDry): int
    {
        $this->assertModuleData($moduleTransfers);

        $manifests = $this->manifestReader->readManifests($moduleTransfers);
        $sprykerLock = $this->sprykerLockReader->getLockFileData();

        $unappliedManifests = $this->findUnappliedManifests($manifests, $sprykerLock);

        if (!$unappliedManifests) {
            return 0;
        }

        if (!$inputOutput->confirm('There are unapplied manifests found for your modules. Do you want to apply them?')) {
            return 0;
        }

        $GLOBALS["IO"] = $inputOutput;
        foreach ($unappliedManifests as $moduleName => $moduleManifests) {
            foreach ($moduleManifests as $manifestType => $unappliedManifestByType) {
                $manifestExecutor = $this->resolveExecutor($manifestType);
                foreach ($unappliedManifestByType as $manifestHash => $unappliedManifest) {
                    if ($manifestExecutor->apply($unappliedManifest, $moduleName, $inputOutput, $isDry)) {
                        $sprykerLock[$moduleName][$manifestType][$manifestHash] = $unappliedManifest;
                    }
                }
            }
        }
        if ($isDry) {
            return 0;
        }

        return $this->sprykerLockWriter->storeLock($sprykerLock);
    }

    /**
     * @param string[][][] $manifests
     * @param string[][] $sprykerLock
     *
     * @return string[][][][]
     */
    protected function findUnappliedManifests(array $manifests, array $sprykerLock): array
    {
        $unappliedManifests = [];
        foreach ($manifests as $moduleName => $manifestList) {
            foreach ($manifestList as $manifestType => $moduleManifests) {
                foreach ($moduleManifests as $moduleManifest) {
                    $manifestHash = sha1(json_encode($moduleManifest) . $manifestType . $moduleName);
                    if (isset($sprykerLock[$moduleName][$manifestType][$manifestHash])) {
                        continue;
                    }

                    $unappliedManifests[$moduleName][$manifestType][$manifestHash] = $moduleManifest;
                }
            }
        }

        return $unappliedManifests;
    }

    /**
     * @param string $manifestType
     *
     * @return \SprykerSdk\Zed\Integrator\Business\ManifestStrategy\ManifestStrategyInterface
     */
    protected function resolveExecutor(string $manifestType): ManifestStrategyInterface
    {
        foreach ($this->manifestExecutors as $manifestExecutor) {
            if ($manifestType === $manifestExecutor->getType()) {
                return $manifestExecutor;
            }
        }

        throw new RuntimeException("Executor $manifestType not found");
    }

    /**
     * @param \Generated\Shared\Transfer\ModuleTransfer[] $moduleTransfers
     *
     * @return void
     */
    protected function assertModuleData(array $moduleTransfers): void
    {
        foreach ($moduleTransfers as $moduleTransfer) {
            $moduleTransfer->requireName()
                ->requireNameDashed()
                ->requireOrganization()
                ->getOrganization()
                ->requireNameDashed()
                ->requireName();
        }
    }
}
