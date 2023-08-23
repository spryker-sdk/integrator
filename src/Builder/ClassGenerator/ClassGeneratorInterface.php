<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassGenerator;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface ClassGeneratorInterface
{
    /**
     * @param string $className
     * @param string|null $parentClass
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function generateClass(string $className, ?string $parentClass = null): ClassInformationTransfer;
}
