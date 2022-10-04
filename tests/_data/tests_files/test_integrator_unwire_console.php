<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorUnwireConsoleCommands;

use Spryker\Zed\TestIntegratorUnwireConsoleCommands\Console\TestPlainConsole;
use Spryker\Zed\TestIntegratorUnwireConsoleCommands\Console\TestDevConsole;
use Spryker\Zed\TestIntegratorUnwireConsoleCommands\Console\TestClassExistsConsole;

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
