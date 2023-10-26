<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Resolver;

use SprykerSdk\Integrator\Transfer\ClassInformationTransfer;

interface PrefixedConstNameResolverInterface
{
    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassInformationTransfer $classInformationTransfer
     * @param string $className
     * @param string $constantName
     *
     * @return string
     */
    public function resolveClassConstantName(ClassInformationTransfer $classInformationTransfer, string $className, string $constantName): string;
}
