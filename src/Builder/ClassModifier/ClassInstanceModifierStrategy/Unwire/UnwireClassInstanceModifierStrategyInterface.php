<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Unwire;

use SprykerSdk\Integrator\Builder\ClassModifier\ClassInstanceModifierStrategy\Applicable\ApplicableModifierStrategyInterface;
use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

interface UnwireClassInstanceModifierStrategyInterface extends ApplicableModifierStrategyInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function unwireClassInstance(
        ClassInformationTransfer $classInformationTransfer,
        ClassMetadataTransfer $classMetadataTransfer
    ): ClassInformationTransfer;
}
