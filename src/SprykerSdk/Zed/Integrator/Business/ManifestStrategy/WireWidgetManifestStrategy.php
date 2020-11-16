<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Business\ManifestStrategy;

use SprykerSdk\Zed\Integrator\Dependency\Console\IOInterface;
use SprykerSdk\Zed\Integrator\IntegratorConfig;

class WireWidgetManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'wire-widget';
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
        $targetClassName = '\SprykerShop\Yves\ShopApplication\ShopApplicationDependencyProvider';
        $targetMethodName = 'getGlobalWidgets';

        foreach ($this->config->getProjectNamespaces() as $namespace) {
            $classInformationTransfer = $this->getClassBuilderFacade()->resolveClass($targetClassName, $namespace);
            if (!$classInformationTransfer) {
                continue;
            }

            $classInformationTransfer = $this->getClassBuilderFacade()->wireClassConstant(
                $classInformationTransfer,
                $targetMethodName,
                $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                'class'
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
