<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Pyz\Zed\TestIntegratorConsoleCommands;

use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestClassExistsConsole;
use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestDevConsole;
use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestNewConsole;
use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestNewConsoleWithCondition;
use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestNewConsoleWithMissingCondition;
use Spryker\Zed\TestIntegratorConsoleCommands\Console\TestPlainConsole;

class ConsoleDependencyProvider
{
    protected function getConsoleCommands(Container $container): array
    {
        $commands = [
            new TestNewConsole(),
        ];
        $commands[] = new TestPlainConsole();

        if ($this->getConfig()->isDevelopmentConsoleCommandsEnabled()) {
            $commands[] = new TestNewConsoleWithCondition();
            $commands[] = new TestDevConsole();
            if (class_exists(TestClassExistsConsole::class)) {
                $commands[] = new TestClassExistsConsole();
            }
        }

        return $commands;
    }
}
