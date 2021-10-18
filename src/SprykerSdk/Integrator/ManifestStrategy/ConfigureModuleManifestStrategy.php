<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use ReflectionClassConstant;
use ReflectionException;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

class ConfigureModuleManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'configure-module';
    }

    /**
     * @param array<string> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        [$targetClassName, $targetPointName] = explode('::', $manifest[IntegratorConfig::MANIFEST_KEY_TARGET]);

        $value = $manifest[IntegratorConfig::MANIFEST_KEY_VALUE] ?? null;
        $choices = $manifest[IntegratorConfig::MANIFEST_KEY_CHOICES] ?? [];
        $defaultValue = $manifest[IntegratorConfig::MANIFEST_KEY_DEFAULT_VALUE] ?? null;

        $applied = false;
        foreach ($this->config->getProjectNamespaces() as $namespace) {
            $classInformationTransfer = $this->createClassBuilderFacade()->resolveClass($targetClassName, $namespace);

            if (!$classInformationTransfer) {
                continue;
            }

            if (!$value) {
                $value = $this->askValue(
                    'Provide value for ' . $classInformationTransfer->getClassName() . "::$targetPointName() configuration.",
                    $choices,
                    $inputOutput,
                    $defaultValue
                );
            }

            if (method_exists($targetClassName, $targetPointName)) {
                $classInformationTransfer = $this->adjustMethod($classInformationTransfer, $targetPointName, $value);
            } elseif ($this->constantExists($manifest[IntegratorConfig::MANIFEST_KEY_TARGET])) {
                $classInformationTransfer = $this->createClassBuilderFacade()->setConstant($classInformationTransfer, $targetPointName, $value);
            } else {
                continue;
            }

            if ($isDry) {
                $inputOutput->writeln($this->createClassBuilderFacade()->printDiff($classInformationTransfer));
                $applied = true;
            } else {
                $applied = $this->createClassBuilderFacade()->storeClass($classInformationTransfer);
            }

            $inputOutput->writeln(sprintf(
                'Configuration was added to %s::%s',
                $classInformationTransfer->getClassName(),
                $targetPointName
            ), InputOutputInterface::DEBUG);
        }

        return $applied;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $targetPointName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    protected function adjustMethod(ClassInformationTransfer $classInformationTransfer, string $targetPointName, $value): ClassInformationTransfer
    {
        if (is_string($value) && strpos($value, '::')) {
            [$className, $constantName] = explode('::', $value);

            return $this->createClassBuilderFacade()->wireClassConstant(
                $classInformationTransfer,
                $targetPointName,
                $className,
                $constantName
            );
        }

        return $this->createClassBuilderFacade()->setMethodReturnValue(
            $classInformationTransfer,
            $targetPointName,
            $value
        );
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    protected function adjustConstant(ClassInformationTransfer $classInformationTransfer, string $constantName, $value): ClassInformationTransfer
    {
        return $this->createClassBuilderFacade()->setConstant($classInformationTransfer, $constantName, $value);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function constantExists(string $value): bool
    {
        [$className, $constantName] = explode('::', $value);

        try {
            new ReflectionClassConstant($className, $constantName);

            return true;
        } catch (ReflectionException $e) {
        }

        return false;
    }
}
