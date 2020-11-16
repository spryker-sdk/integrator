<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business\ManifestStrategy;

use Generated\Shared\Transfer\ClassInformationTransfer;
use ReflectionClass;
use SprykerSdk\Zed\Integrator\Business\Builder\Helper\ClassHelper;
use SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface;
use SprykerSdk\Zed\Integrator\IntegratorConfig;

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
     * @param \SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, IOInterface $inputOutput, bool $isDry): bool
    {
        [$targetClassName, $targetMethodName] = explode('::', $manifest[IntegratorConfig::MANIFEST_KEY_TARGET]);

        $classHelper = new ClassHelper();
        if (!class_exists($targetClassName)) {
            $inputOutput->writeln(sprintf(
                'Target module %s/%s does not exists in your system.',
                $classHelper->getOrganisationName($targetClassName),
                $classHelper->getModuleName($targetClassName)
            ), IOInterface::DEBUG);

            return false;
        }

        $targetClassInfo = (new ReflectionClass($targetClassName));

        if (!$targetClassInfo->hasMethod($targetMethodName)) {
            $inputOutput->writeln(sprintf(
                'Your version of module %s/%s does not support needed plugin stack. Please, update it to use full functionality.',
                $classHelper->getOrganisationName($targetClassName),
                $classHelper->getModuleName($targetClassName)
            ), IOInterface::DEBUG);

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
            ), IOInterface::DEBUG);
        }

        return true;
    }
}
