<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IntegratorLockUpdaterConsole extends AbstractInstallerConsole
{
    /**
     * @var string
     */
    protected const COMMAND_NAME = 'manifest:lock:run';

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::COMMAND_NAME)
            ->setDescription('Run integrator lock generation to sync project state.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandArgumentsTransfer = $this->buildCommandArgumentsTransfer($input);
        $io = $this->createInputOutputAdapter($input, $output, $commandArgumentsTransfer->getFormat());
        $this->getFacade()
            ->runCleanLock($io);

        $this->getFacade()->runUpdateLock(
            $io,
            $commandArgumentsTransfer,
        );

        return 0;
    }
}
