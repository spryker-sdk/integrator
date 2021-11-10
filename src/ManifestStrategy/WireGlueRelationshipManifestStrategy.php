<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;

class WireGlueRelationshipManifestStrategy extends AbstractManifestStrategy
{
    /**
     * @var string
     */
    protected const TARGET_CLASS_NAME = '\Spryker\Glue\GlueApplication\GlueApplicationDependencyProvider';

    /**
     * @var string
     */
    protected const TARGET_METHOD_NAME = 'getResourceRelationshipPlugins';

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'wire-glue-relationship';
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

            $targetClass = $manifest[IntegratorConfig::MANIFEST_KEY_SOURCE];
            $targetClassKey = null;
            if (is_array($targetClass)) {
                foreach ($targetClass as $key => $class) {
                    $targetClass = $class;
                    $targetClassKey = $key;

                    break;
                }
            }
            if (!defined($targetClassKey)) {
                continue;
            }

            $classInformationTransfer = $this->createClassBuilderFacade()->wireGlueRelationship(
                $classInformationTransfer,
                static::TARGET_METHOD_NAME,
                $targetClassKey,
                $targetClass,
            );

            if ($isDry) {
                $applied = $inputOutput->writeln($this->createClassBuilderFacade()->printDiff($classInformationTransfer));
            } else {
                $applied = $this->createClassBuilderFacade()->storeClass($classInformationTransfer);
            }

            $inputOutput->writeln(sprintf(
                'GLUE relationship %s was added to %s::%s',
                $targetClassKey,
                $classInformationTransfer->getClassName(),
                static::TARGET_METHOD_NAME,
            ), InputOutputInterface::DEBUG);
        }

        return $applied;
    }
}
