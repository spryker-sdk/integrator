<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorConsoleCommands;

use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestPlainConsole;
use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestDevConsole;
use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestClassExistsConsole;

class ConsoleDependencyProvider
{
    protected function getConsoleCommands(Container $container): array
    {
        $commands = [];

        if ($this->getConfig()->isDevelopmentConsoleCommandsEnabled()) {
            if (class_exists(TestClassExistsConsole::class)) {
            }
        }

        return $commands;
    }
}
