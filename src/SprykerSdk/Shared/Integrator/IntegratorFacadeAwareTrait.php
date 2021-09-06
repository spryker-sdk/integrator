<?php

declare(strict_types = 1);

namespace SprykerSdk\Shared\Integrator;

use SprykerSdk\Integrator\Business\IntegratorFacade;

trait IntegratorFacadeAwareTrait
{
    /**
     * @return \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    protected function getFactory(): IntegratorFacade
    {
        return new IntegratorFacade();
    }
}
