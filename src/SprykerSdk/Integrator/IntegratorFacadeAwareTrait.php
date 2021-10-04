<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Integrator;

use SprykerSdk\Integrator\IntegratorFacade;
use SprykerSdk\Integrator\IntegratorFacadeInterface;

trait IntegratorFacadeAwareTrait
{
    /**
     * @var \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    protected $facade;

    /**
     * @return \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    protected function getFacade(): IntegratorFacadeInterface
    {
        if ($this->facade === null) {
            $this->facade = new IntegratorFacade();
        }

        return $this->facade;
    }
}
