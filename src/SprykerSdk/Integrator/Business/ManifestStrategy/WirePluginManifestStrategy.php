<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Business\ManifestStrategy;

use ReflectionClass;
use SprykerSdk\Integrator\Business\Helper\ClassHelper;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class WirePluginManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'wire-plugin';
    }

    /**
     * @param string[] $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        [$targetClassName, $targetMethodName] = explode('::', $manifest[IntegratorConfig::MANIFEST_KEY_TARGET]);

        $classHelper = new ClassHelper();
        if (!class_exists($targetClassName)) {
            $inputOutput->writeln(sprintf(
                'Target module %s/%s does not exists in your system.',
                $classHelper->getOrganisationName($targetClassName),
                $classHelper->getModuleName($targetClassName)
            ), InputOutputInterface::DEBUG);

            return false;
        }

        $targetClassInfo = (new ReflectionClass($targetClassName));

        if (!$targetClassInfo->hasMethod($targetMethodName)) {
            $inputOutput->writeln(sprintf(
                'Your version of module %s/%s does not support needed plugin stack. Please, update it to use full functionality.',
                $classHelper->getOrganisationName($targetClassName),
                $classHelper->getModuleName($targetClassName)
            ), InputOutputInterface::DEBUG);

            return false;
        }

        foreach ($this->config->getProjectNamespaces() as $namespace) {
            $classInformationTransfer = $this->getClassBuilderFacade()->resolveClass($targetClassName, $namespace);
            if (!$classInformationTransfer) {
                continue;
            }

            $classInformationTransfer = $this->getClassBuilderFacade()->wireClassInstance(
                $classInformationTransfer,
                $targetMethodName,
                $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                $manifest[IntegratorConfig::MANIFEST_KEY_POSITION][IntegratorConfig::MANIFEST_KEY_POSITION_BEFORE] ?? '',
                $manifest[IntegratorConfig::MANIFEST_KEY_POSITION][IntegratorConfig::MANIFEST_KEY_POSITION_AFTER] ?? ''
            );

            if ($isDry) {
                $inputOutput->writeln($this->getClassBuilderFacade()->printDiff($classInformationTransfer));
            } else {
                $this->getClassBuilderFacade()->storeClass($classInformationTransfer);
            }

            $inputOutput->writeln(sprintf(
                'Plugin %s was added to %s::%s',
                $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                $classInformationTransfer->getClassName(),
                $targetMethodName
            ), InputOutputInterface::DEBUG);
        }

        return true;
    }
}
