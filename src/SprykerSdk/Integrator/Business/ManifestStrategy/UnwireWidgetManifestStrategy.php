<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Business\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class UnwireWidgetManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'unwire-widget';
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

        $applied = false;
        foreach ($this->config->getProjectNamespaces() as $namespace) {
            $classInformationTransfer = $this->getClassBuilderFacade()->resolveClass($targetClassName, $namespace);
            if (!$classInformationTransfer) {
                continue;
            }

            $classInformationTransfer = $this->getClassBuilderFacade()->unwireClassConstant(
                $classInformationTransfer,
                $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                $targetMethodName
            );

            if ($isDry) {
                $applied = $inputOutput->writeln($this->getClassBuilderFacade()->printDiff($classInformationTransfer));
            } else {
                $applied = $this->getClassBuilderFacade()->storeClass($classInformationTransfer);
            }

            $inputOutput->writeln(sprintf(
                'Widget %s was added to %s::%s',
                $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                $classInformationTransfer->getClassName(),
                $targetMethodName
            ), InputOutputInterface::DEBUG);
        }

        return $applied;
    }
}
