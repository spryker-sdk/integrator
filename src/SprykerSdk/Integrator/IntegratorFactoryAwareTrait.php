<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Integrator;

trait IntegratorFactoryAwareTrait
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorFactory
     */
    protected $factory;

    /**
     * @return \SprykerSdk\Integrator\IntegratorFactory
     */
    protected function getFactory(): IntegratorFactory
    {
        if ($this->factory === null) {
            $this->factory = new IntegratorFactory();
        }

        return $this->factory;
    }
}
