<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\ModuleFinder\Business\Package\PackageFinder;

interface PackageFinderInterface
{
    /**
     * @return \Shared\Transfer\PackageTransfer[]
     */
    public function getPackages(): array;
}
