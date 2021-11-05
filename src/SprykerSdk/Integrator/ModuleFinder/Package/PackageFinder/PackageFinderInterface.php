<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder\Package\PackageFinder;

interface PackageFinderInterface
{
    /**
     * @return array<\SprykerSdk\Integrator\Transfer\PackageTransfer>
     */
    public function getPackages(): array;
}
