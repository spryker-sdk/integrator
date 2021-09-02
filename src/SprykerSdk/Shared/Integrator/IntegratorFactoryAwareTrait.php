<?php

declare(strict_types = 1);

namespace SprykerSdk\Shared\Intergator;

use SprykerSdk\Integrator\IntegratorFactory;

trait IntegratorFactoryAwareTrait
{
    /**
     * @return IntegratorFactory
     */
    protected function getFactory(): IntegratorFactory
    {
        return new IntegratorFactory();
    }
}
