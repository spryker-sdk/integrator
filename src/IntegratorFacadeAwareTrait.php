<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator;

trait IntegratorFacadeAwareTrait
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorFacadeInterface
     */
    protected $facade;

    /**
     * @return \SprykerSdk\Integrator\IntegratorFacadeInterface
     */
    protected function getFacade(): IntegratorFacadeInterface
    {
        if ($this->facade === null) {
            $this->facade = new IntegratorFacade();
        }

        return $this->facade;
    }
}
