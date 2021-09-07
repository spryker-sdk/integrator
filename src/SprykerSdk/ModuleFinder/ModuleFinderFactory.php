<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\ModuleFinder;

use SprykerSdk\ModuleFinder\Business\Module\ModuleFinder\ModuleFinder;
use SprykerSdk\ModuleFinder\Business\Module\ModuleFinder\ModuleFinderInterface;
use SprykerSdk\ModuleFinder\Business\Module\ModuleMatcher\ModuleMatcher;
use SprykerSdk\ModuleFinder\Business\Module\ModuleMatcher\ModuleMatcherInterface;
use SprykerSdk\ModuleFinder\Business\Module\ProjectModuleFinder\ProjectModuleFinder;
use SprykerSdk\ModuleFinder\Business\Module\ProjectModuleFinder\ProjectModuleFinderInterface;
use SprykerSdk\ModuleFinder\Business\Package\PackageFinder\PackageFinder;
use SprykerSdk\ModuleFinder\Business\Package\PackageFinder\PackageFinderInterface;
use SprykerSdk\ModuleFinder\ModuleFinderConfig;

class ModuleFinderFactory
{
    /**
     * @return \SprykerSdk\ModuleFinder\ModuleFinderConfig
     */
    public function getConfig(): ModuleFinderConfig
    {
        return new ModuleFinderConfig();
    }

    /**
     * @return \SprykerSdk\ModuleFinder\Business\Module\ModuleFinder\ModuleFinderInterface
     */
    public function createModuleFinder(): ModuleFinderInterface
    {
        return new ModuleFinder($this->getConfig(), $this->createModuleMatcher());
    }

    /**
     * @return \SprykerSdk\ModuleFinder\Business\Module\ModuleMatcher\ModuleMatcherInterface
     */
    public function createModuleMatcher(): ModuleMatcherInterface
    {
        return new ModuleMatcher();
    }

    /**
     * @return \SprykerSdk\ModuleFinder\Business\Package\PackageFinder\PackageFinderInterface
     */
    public function createPackageFinder(): PackageFinderInterface
    {
        return new PackageFinder($this->getConfig());
    }

    /**
     * @return \SprykerSdk\ModuleFinder\Business\Module\ProjectModuleFinder\ProjectModuleFinderInterface
     */
    public function createProjectModuleFinder(): ProjectModuleFinderInterface
    {
        return new ProjectModuleFinder($this->getConfig(), $this->createModuleMatcher());
    }
}
