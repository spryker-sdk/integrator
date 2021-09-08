<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Shared\Common;

abstract class AbstractConfig
{
    /**
     * @var self|null
     */
    protected static $instance;

    /**
     * @api
     *
     * @return \SprykerSdk\Shared\Common\AbstractConfig
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
