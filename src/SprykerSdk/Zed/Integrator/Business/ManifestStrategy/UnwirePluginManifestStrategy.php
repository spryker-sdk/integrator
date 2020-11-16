<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business\ManifestStrategy;

use SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface;
use SprykerSdk\Zed\Integrator\IntegratorConfig;

class UnwirePluginManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'unwire-plugin';
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

        $applied = false;
        foreach ($this->config->getProjectNamespaces() as $namespace) {
            $classInformationTransfer = $this->getClassBuilderFacade()->resolveClass($targetClassName, $namespace);
            if (!$classInformationTransfer) {
                continue;
            }

            $classInformationTransfer = $this->getClassBuilderFacade()->unwireClassInstance($classInformationTransfer, $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE], $targetMethodName);
            if ($classInformationTransfer) {
                if ($isDry) {
                    $applied = $inputOutput->writeln($this->getClassBuilderFacade()->printDiff($classInformationTransfer));
                } else {
                    $applied = $this->getClassBuilderFacade()->storeClass($classInformationTransfer);
                }
                $inputOutput->writeln(sprintf(
                    'Plugin %s was removed from %s::%s',
                    $targetClassName,
                    $classInformationTransfer->getClassName(),
                    $targetMethodName
                ), IOInterface::DEBUG);
            }
        }

        return $applied;
    }
}
