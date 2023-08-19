<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use ReflectionClass;
use SprykerSdk\Integrator\Builder\ClassMetadataBuilder\ClassMetadataBuilderInterface;
use SprykerSdk\Integrator\Builder\ComposerClassLoader\ComposerClassLoader;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class UnwirePluginManifestStrategy extends AbstractManifestStrategy
{
    protected ClassMetadataBuilderInterface $metadataBuilder;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \SprykerSdk\Integrator\Helper\ClassHelperInterface $classHelper
     * @param \SprykerSdk\Integrator\Builder\ClassMetadataBuilder\ClassMetadataBuilderInterface $metadataBuilder
     */
    public function __construct(
        IntegratorConfig $config,
        ClassHelperInterface $classHelper,
        ClassMetadataBuilderInterface $metadataBuilder
    ) {
        parent::__construct($config, $classHelper);
        $this->metadataBuilder = $metadataBuilder;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'unwire-plugin';
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
        /** @phpstan-var class-string $targetClassName */
        [$targetClassName, $targetMethodName] = explode('::', $manifest[IntegratorConfig::MANIFEST_KEY_TARGET]);

        if (!ComposerClassLoader::classExist($targetClassName)) {
            $inputOutput->writeln(sprintf(
                'Target module %s/%s does not exists in your system.',
                $this->classHelper->getOrganisationName($targetClassName),
                $this->classHelper->getModuleName($targetClassName),
            ), InputOutputInterface::DEBUG);

            return false;
        }

        $targetClassInfo = (new ReflectionClass($targetClassName));

        if (!$targetClassInfo->hasMethod($targetMethodName)) {
            $targetMethodNameExistOnProjectLayer = false;
            foreach ($this->config->getProjectNamespaces() as $namespace) {
                $classInformationTransfer = $this->createClassBuilderFacade()->resolveClass($targetClassName, $namespace);
                if ($classInformationTransfer) {
                    $targetMethodNameExistOnProjectLayer = true;

                    break;
                }
            }

            if (!$targetMethodNameExistOnProjectLayer) {
                $inputOutput->writeln(sprintf(
                    'Your version of module %s/%s does not support needed plugin stack. Please, update it to use full functionality.',
                    $this->classHelper->getOrganisationName($targetClassName),
                    $this->classHelper->getModuleName($targetClassName),
                ), InputOutputInterface::DEBUG);

                return false;
            }
        }

        $applied = false;
        foreach ($this->config->getProjectNamespaces() as $namespace) {
            $classInformationTransfer = $this->createClassBuilderFacade()->resolveClass($targetClassName, $namespace);
            if ($classInformationTransfer === null) {
                continue;
            }

            $classMetadataTransfer = $this->metadataBuilder->build($manifest);

            $classInformationTransfer = $this->createClassBuilderFacade()->unwireClassInstance(
                $classInformationTransfer,
                $classMetadataTransfer,
            );

            if ($classInformationTransfer) {
                if ($isDry) {
                    $diff = $this->createClassBuilderFacade()->printDiff($classInformationTransfer);
                    if ($diff) {
                        $inputOutput->writeln($diff);
                    }
                } else {
                    $applied = $this->createClassBuilderFacade()->storeClass($classInformationTransfer);
                }
                $inputOutput->writeln(sprintf(
                    'Plugin %s was removed from %s::%s',
                    $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE],
                    $targetClassName,
                    $targetMethodName,
                ), InputOutputInterface::DEBUG);
            }
        }

        return $applied;
    }
}
