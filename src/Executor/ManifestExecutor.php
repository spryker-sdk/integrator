<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Executor;

use Exception;
use RuntimeException;
use SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizersExecutorInterface;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;

class ManifestExecutor implements ManifestExecutorInterface
{
    /**
     * @var array<\SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface>
     */
    protected $manifestExecutors;

    /**
     * @var \SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizersExecutorInterface
     */
    protected $fileNormalizersExecutor;

    /**
     * @param \SprykerSdk\Integrator\Builder\FileNormalizer\FileNormalizersExecutorInterface $fileNormalizersExecutor
     * @param array<\SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface> $manifestExecutors
     */
    public function __construct(
        FileNormalizersExecutorInterface $fileNormalizersExecutor,
        array $manifestExecutors
    ) {
        $this->manifestExecutors = $manifestExecutors;
        $this->fileNormalizersExecutor = $fileNormalizersExecutor;
    }

    /**
     * @param array<string, array<string, array<string, mixed>>> $lockedModules
     * @param array<string, array<string, array<string, mixed>>> $unappliedManifests
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function applyManifestList(
        array $lockedModules,
        array $unappliedManifests,
        InputOutputInterface $inputOutput,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): array {
        if (!$unappliedManifests) {
            return $lockedModules;
        }

        if (!$inputOutput->confirm('There are unapplied manifests found for your modules. Do you want to apply them?')) {
            return $lockedModules;
        }

        $isDry = $commandArgumentsTransfer->getIsDryOrFail();

        foreach ($unappliedManifests as $moduleName => $moduleManifests) {
            foreach ($moduleManifests as $manifestType => $unappliedManifestByType) {
                $lockedModules = $this->applyManifestsByType($lockedModules, $unappliedManifestByType, $manifestType, $moduleName, $inputOutput, $isDry);
            }
        }

        $this->fileNormalizersExecutor->execute($inputOutput, $isDry);

        return $lockedModules;
    }

    /**
     * @param array<string, array<string, array<string, mixed>>> $lockedModules
     * @param array $unappliedManifestByType
     * @param string $manifestType
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    protected function applyManifestsByType(
        array $lockedModules,
        array $unappliedManifestByType,
        string $manifestType,
        string $moduleName,
        InputOutputInterface $inputOutput,
        bool $isDry
    ): array {
        try {
            $manifestExecutor = $this->resolveExecutor($manifestType);
        } catch (RuntimeException $runtimeException) {
            $inputOutput->warning($runtimeException->getMessage());

            return $lockedModules;
        }

        foreach ($unappliedManifestByType as $manifestHash => $unappliedManifest) {
            try {
                if ($manifestExecutor->apply($unappliedManifest, $moduleName, $inputOutput, $isDry)) {
                    $lockedModules[$moduleName][$manifestType][$manifestHash] = $unappliedManifest;
                }
            } catch (Exception $exception) {
                $inputOutput->warning(sprintf(
                    'Manifest for %s:%s was skipped. %s',
                    $unappliedManifest[IntegratorConfig::MODULE_KEY],
                    $unappliedManifest[IntegratorConfig::MODULE_VERSION_KEY],
                    $exception->getMessage(),
                ));
            }
        }

        return $lockedModules;
    }

    /**
     * @param array<string, array<string, array<string>>> $manifests
     * @param array<string, array<string, array<string>>> $lockedModules
     *
     * @return array<string, array<string, array<string, array<string>>>>
     */
    public function findUnappliedManifests(array $manifests, array $lockedModules): array
    {
        $unappliedManifests = [];
        foreach ($manifests as $moduleName => $manifestList) {
            foreach ($manifestList as $manifestType => $moduleManifests) {
                /** @var array<string> $moduleManifest */
                foreach ($moduleManifests as $moduleManifest) {
                    $manifestHash = sha1(json_encode($moduleManifest) . $manifestType . $moduleName);
                    if (isset($lockedModules[$moduleName][$manifestType][$manifestHash])) {
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
     * @throws \RuntimeException
     *
     * @return \SprykerSdk\Integrator\ManifestStrategy\ManifestStrategyInterface
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
}
