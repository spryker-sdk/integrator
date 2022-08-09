<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassMetadataBuilder;

use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class ClassMetadataBuilder implements ClassMetadataBuilderInterface
{
    /**
     * @var array<\SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin\ClassMetadataBuilderPluginInterface>
     */
    protected array $builderPlugins = [];

    /**
     * @param array<\SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin\ClassMetadataBuilderPluginInterface> $builderPlugins
     */
    public function __construct(array $builderPlugins)
    {
        $this->builderPlugins = $builderPlugins;
    }

    /**
     * @param array<mixed> $manifest
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    public function build(array $manifest): ClassMetadataTransfer
    {
        $transfer = new ClassMetadataTransfer();

        foreach ($this->builderPlugins as $builderPlugin) {
            $builderPlugin->build($manifest, $transfer);
        }

        return $transfer;
    }
}
