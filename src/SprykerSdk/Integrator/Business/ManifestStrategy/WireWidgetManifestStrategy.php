<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Business\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;

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
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
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
            ), InputOutputInterface::DEBUG);
        }

        return true;
    }
}
