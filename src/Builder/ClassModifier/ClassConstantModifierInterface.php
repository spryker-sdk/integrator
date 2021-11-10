<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface ClassConstantModifierInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $constantName
     * @param mixed $value
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function setConstant(ClassInformationTransfer $classInformationTransfer, string $constantName, $value): ClassInformationTransfer;
}
