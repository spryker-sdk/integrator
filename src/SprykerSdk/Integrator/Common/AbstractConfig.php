<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Common;

abstract class AbstractConfig
{
    /**
     * @var self|null
     */
    protected static $instance;

    /**
     * @return \SprykerSdk\Integrator\Common\AbstractConfig
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
            static::$instance->loadConfig();
        }

        return static::$instance;
    }
}
