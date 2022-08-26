<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin;

use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class PositionBuilderPlugin implements ClassMetadataBuilderPluginInterface
{
    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    public function build(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        $transfer->setBefore(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_POSITION][IntegratorConfig::MANIFEST_KEY_POSITION_BEFORE] ?? '', '\\'));
        $transfer->setAfter(ltrim($manifest[IntegratorConfig::MANIFEST_KEY_POSITION][IntegratorConfig::MANIFEST_KEY_POSITION_AFTER] ?? '', '\\'));
        $transfer->setCondition($manifest[IntegratorConfig::MANIFEST_KEY_CONDITION] ?? null);

        return $transfer;
    }
}
