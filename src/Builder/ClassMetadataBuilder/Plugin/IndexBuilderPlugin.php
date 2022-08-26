<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassMetadataBuilder\Plugin;

use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class IndexBuilderPlugin implements ClassMetadataBuilderPluginInterface
{
    /**
     * @param array<mixed> $manifest
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $transfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    public function build(array $manifest, ClassMetadataTransfer $transfer): ClassMetadataTransfer
    {
        $transfer->setIndex($manifest[IntegratorConfig::MANIFEST_KEY_INDEX] ?? null);

        return $transfer;
    }
}
