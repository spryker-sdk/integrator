<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassLoader;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface ClassLoaderInterface
{
    /**
     * @param string $className
     *
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer
     */
    public function loadClass(string $className): ClassInformationTransfer;
}
