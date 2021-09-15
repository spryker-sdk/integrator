<?php

declare(strict_types = 1);

namespace SprykerSdk\Shared\Integrator;

use SprykerSdk\Integrator\Business\IntegratorFacade;
use SprykerSdk\Integrator\Business\IntegratorFacadeInterface;

trait IntegratorFacadeAwareTrait
{
    /**
     * @return \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    protected function getFacade(): IntegratorFacadeInterface
    {
        return new IntegratorFacade();
    }
}
