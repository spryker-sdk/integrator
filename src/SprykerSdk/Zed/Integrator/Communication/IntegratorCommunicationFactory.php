<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Zed\Integrator\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerSdk\Zed\Integrator\Dependency\Facade\IntegratorToModuleFinderFacadeInterface;
use SprykerSdk\Zed\Integrator\IntegratorDependencyProvider;

/**
 * @method \SprykerSdk\Zed\Integrator\IntegratorConfig getConfig()
 */
class IntegratorCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerSdk\Zed\Integrator\Dependency\Facade\IntegratorToModuleFinderFacadeInterface
     */
    public function getModuleFinderFacade(): IntegratorToModuleFinderFacadeInterface
    {
        return $this->getProvidedDependency(IntegratorDependencyProvider::FACADE_MODULE_FINDER);
    }
}
