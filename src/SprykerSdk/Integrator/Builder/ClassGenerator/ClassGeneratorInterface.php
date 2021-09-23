<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator\Builder\ClassGenerator;

interface ClassGeneratorInterface
{
    /**
     * @param string $className
     * @param string|null $parentClass
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function generateClass(string $className, ?string $parentClass = null);
}
