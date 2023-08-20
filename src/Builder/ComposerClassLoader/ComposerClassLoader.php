<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ComposerClassLoader;

use Composer\Autoload\ClassLoader;

final class ComposerClassLoader
{
    private static ?ClassLoader $composerClassLoader = null;

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    private static function getComposerClassLoader(): ClassLoader
    {
        if (static::$composerClassLoader === null) {
            static::$composerClassLoader = require file_exists(APPLICATION_ROOT_DIR . '/vendor/autoload.php') ?
                APPLICATION_ROOT_DIR . '/vendor/autoload.php' :
                INTEGRATOR_ROOT_DIR . '/vendor/autoload.php';
        }

        return static::$composerClassLoader;
    }

    /**
     * @param string $className
     *
     * @return string|null
     */
    public static function getFilePath(string $className): ?string
    {
        return static::getComposerClassLoader()->findFile(ltrim($className, '\\')) ?: null;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public static function classExist(string $className): bool
    {
        return (bool)static::getFilePath($className);
    }
}
