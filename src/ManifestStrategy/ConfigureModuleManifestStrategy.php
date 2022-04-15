<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;

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
     * @param array<mixed> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        [$targetClassName, $targetPointName] = explode('::', $manifest[IntegratorConfig::MANIFEST_KEY_TARGET]);

        $previousValue = $manifest[IntegratorConfig::MANIFEST_KEY_PREVIOUS_VALUE] ?? null;
        $value = $manifest[IntegratorConfig::MANIFEST_KEY_VALUE] ?? null;
        $choices = $manifest[IntegratorConfig::MANIFEST_KEY_CHOICES] ?? [];
        $defaultValue = $manifest[IntegratorConfig::MANIFEST_KEY_DEFAULT_VALUE] ?? null;
        $isLiteral = false;
        if ($this->isLiteralManifestValue($value)) {
            $isLiteral = $manifest[IntegratorConfig::MANIFEST_KEY_VALUE][IntegratorConfig::MANIFEST_KEY_IS_LITERAL] ?? false;
            $value = $value[IntegratorConfig::MANIFEST_KEY_VALUE];
        }

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
                    $defaultValue,
                );
            }

            if ($this->isConstant($targetPointName)) {
                $classInformationTransfer = $this->createClassBuilderFacade()
                    ->setConstant($classInformationTransfer, $targetPointName, $value);
            } else {
                $classInformationTransfer = $this->createClassBuilderFacade()->setMethodReturnValue(
                    $classInformationTransfer,
                    $targetPointName,
                    $value,
                    $isLiteral,
                    $previousValue,
                );
            }

            if ($isDry) {
                $diff = $this->createClassBuilderFacade()->printDiff($classInformationTransfer);
                if ($diff) {
                    $inputOutput->writeln($diff);
                }
                $applied = true;
            } else {
                $applied = $this->createClassBuilderFacade()->storeClass($classInformationTransfer);
            }

            $inputOutput->writeln(sprintf(
                'Configuration was added to %s::%s',
                $classInformationTransfer->getClassName(),
                $targetPointName,
            ), InputOutputInterface::DEBUG);
        }

        return $applied;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isLiteralManifestValue($value): bool
    {
        return is_array($value) && isset($value[IntegratorConfig::MANIFEST_KEY_IS_LITERAL]);
    }

    /**
     * @param mixed $value
     * @param bool $isLiteral
     *
     * @return bool
     */
    protected function isConstantValue($value, bool $isLiteral): bool
    {
        return !$isLiteral && is_string($value) && strpos($value, '::');
    }

    /**
     * @param string $targetPointName
     *
     * @return bool
     */
    protected function isConstant(string $targetPointName): bool
    {
        return (bool)preg_match('/^[A-Z\_]+$/', $targetPointName);
    }
}
