<?php

declare(strict_types = 1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Shared\Integrator;

use SprykerSdk\Integrator\Business\IntegratorFacade;

trait IntegratorFacadeAwareTrait
{
    /**
     * @return \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    protected function getFacade(): IntegratorFacade
    {
        return new IntegratorFacade();
    }
}
