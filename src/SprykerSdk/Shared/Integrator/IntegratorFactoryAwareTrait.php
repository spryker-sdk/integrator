<?php

declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Shared\Integrator;

use SprykerSdk\Integrator\IntegratorFactory;

trait IntegratorFactoryAwareTrait
{
    /**
     * @return \SprykerSdk\Integrator\IntegratorFactory
     */
    protected function getFactory(): IntegratorFactory
    {
        return new IntegratorFactory();
    }
}
