<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder;

use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\ModuleFinder\Module\ModuleFinder\ModuleFinder;
use SprykerSdk\Integrator\ModuleFinder\Module\ModuleFinder\ModuleFinderInterface;
use SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcher;
use SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcherInterface;
use SprykerSdk\Integrator\ModuleFinder\Module\ProjectModuleFinder\ProjectModuleFinder;
use SprykerSdk\Integrator\ModuleFinder\Module\ProjectModuleFinder\ProjectModuleFinderInterface;
use SprykerSdk\Integrator\ModuleFinder\Package\PackageFinder\PackageFinder;
use SprykerSdk\Integrator\ModuleFinder\Package\PackageFinder\PackageFinderInterface;

class ModuleFinderFactory
{
    /**
     * @return \SprykerSdk\Integrator\IntegratorConfig
     */
    public function getConfig(): IntegratorConfig
    {
        return IntegratorConfig::getInstance();
    }

    /**
     * @return \SprykerSdk\Integrator\ModuleFinder\Module\ModuleFinder\ModuleFinderInterface
     */
    public function createModuleFinder(): ModuleFinderInterface
    {
        return new ModuleFinder($this->getConfig(), $this->createModuleMatcher());
    }

    /**
     * @return \SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcherInterface
     */
    public function createModuleMatcher(): ModuleMatcherInterface
    {
        return new ModuleMatcher();
    }

    /**
     * @return \SprykerSdk\Integrator\ModuleFinder\Package\PackageFinder\PackageFinderInterface
     */
    public function createPackageFinder(): PackageFinderInterface
    {
        return new PackageFinder($this->getConfig());
    }

    /**
     * @return \SprykerSdk\Integrator\ModuleFinder\Module\ProjectModuleFinder\ProjectModuleFinderInterface
     */
    public function createProjectModuleFinder(): ProjectModuleFinderInterface
    {
        return new ProjectModuleFinder($this->getConfig(), $this->createModuleMatcher());
    }
}
