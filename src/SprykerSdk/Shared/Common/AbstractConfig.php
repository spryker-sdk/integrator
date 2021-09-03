<?php

namespace SprykerSdk\Shared\Common;

abstract class AbstractConfig
{
    /**
     * @var self|null
     */
    private static $instance;

    /**
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
