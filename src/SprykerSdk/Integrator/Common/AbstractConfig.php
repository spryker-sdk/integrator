<?php

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
            static::$instance = (new static())->loadConfig();

        }

        return static::$instance;
    }
}
