<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassMetadataBuilder;

use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

interface ClassMetadataBuilderInterface
{
    /**
     * @param array<mixed> $manifest
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    public function build(array $manifest): ClassMetadataTransfer;
}
