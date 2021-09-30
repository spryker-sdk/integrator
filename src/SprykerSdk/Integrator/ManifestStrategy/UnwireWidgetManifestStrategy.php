<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class UnwireWidgetManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @var string
     */
    protected const TARGET_CLASS_NAME = '\SprykerShop\Yves\ShopApplication\ShopApplicationDependencyProvider';
    protected const TARGET_METHOD_NAME = 'getGlobalWidgets';

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'unwire-widget';
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
        $applied = false;
        foreach ($this->config->getProjectNamespaces() as $namespace) {
            $classInformationTransfer = $this->getClassBuilderFacade()->resolveClass(self::TARGET_CLASS_NAME, $namespace);
            if (!$classInformationTransfer) {
                continue;
            }

            $classInformationTransfer = $this->getClassBuilderFacade()->unwireClassConstant(
                $classInformationTransfer,
                $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                self::TARGET_METHOD_NAME
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
                self::TARGET_METHOD_NAME
            ), InputOutputInterface::DEBUG);
        }

        return $applied;
    }
}
