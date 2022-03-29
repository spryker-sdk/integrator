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

    /**
     * @var string
     */
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
            $classInformationTransfer = $this->createClassBuilderFacade()->resolveClass(static::TARGET_CLASS_NAME, $namespace);
            if (!$classInformationTransfer) {
                continue;
            }

            $classInformationTransfer = $this->createClassBuilderFacade()->unwireClassConstant(
                $classInformationTransfer,
                $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                static::TARGET_METHOD_NAME,
            );
            if (!$classInformationTransfer) {
                continue;
            }

            if ($isDry) {
                $diff = $this->createClassBuilderFacade()->printDiff($classInformationTransfer);
                if ($diff) {
                    $inputOutput->writeln($diff);
                }
            } else {
                $applied = $this->createClassBuilderFacade()->storeClass($classInformationTransfer);
            }

            $inputOutput->writeln(sprintf(
                'Widget %s was removed from %s::%s',
                $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                $classInformationTransfer->getClassName(),
                static::TARGET_METHOD_NAME,
            ), InputOutputInterface::DEBUG);
        }

        return $applied;
    }
}
