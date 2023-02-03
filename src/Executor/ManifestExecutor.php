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
     * @param array<string, array<string, array<string, array<string>>>> $unappliedManifests
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
     *
     * @return void
     */
    public function applyManifestList(
        array $unappliedManifests,
        InputOutputInterface $inputOutput,
        IntegratorCommandArgumentsTransfer $commandArgumentsTransfer
    ): void {
        if (!$unappliedManifests) {
            return;
        }

        if (!$inputOutput->confirm('There are unapplied manifests found for your modules. Do you want to apply them?')) {
            return;
        }

        $isDry = $commandArgumentsTransfer->getIsDryOrFail();

        foreach ($unappliedManifests as $moduleName => $moduleManifests) {
            foreach ($moduleManifests as $manifestType => $unappliedManifestByType) {
                $this->applyManifestsByType($unappliedManifestByType, $manifestType, $moduleName, $inputOutput, $isDry);
            }
        }

        $this->fileNormalizersExecutor->execute($inputOutput, $isDry);
    }

    /**
     * @param array<mixed> $unappliedManifestByType
     * @param string $manifestType
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return void
     */
    protected function applyManifestsByType(
        array $unappliedManifestByType,
        string $manifestType,
        string $moduleName,
        InputOutputInterface $inputOutput,
        bool $isDry
    ): void {
        try {
            $manifestExecutor = $this->resolveExecutor($manifestType);
        } catch (RuntimeException $runtimeException) {
            $inputOutput->warning($runtimeException->getMessage());

            return;
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
