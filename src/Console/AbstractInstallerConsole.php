<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Console;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputJsonOutputAdapter;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use SprykerSdk\Integrator\IntegratorFacadeAwareTrait;
use SprykerSdk\Integrator\IntegratorFactoryAwareTrait;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractInstallerConsole extends Command
{
    use IntegratorFactoryAwareTrait;
    use IntegratorFacadeAwareTrait;

    /**
     * @var string
     */
    public const OPTION_FORMAT = 'format';

    /**
     * @var string
     */
    public const FLAG_DRY = 'dry';

    /**
     * @var string
     */
    public const FORMAT_JSON = 'json';

    /**
     * @var string
     */
    protected const OPTION_FORMAT_DESCRIPTION = 'Define the format of the command output, example: json';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption(static::FLAG_DRY)
            ->addOption(
                static::OPTION_FORMAT,
                null,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_FORMAT_DESCRIPTION,
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer
     */
    protected function buildCommandArgumentsTransfer(InputInterface $input): IntegratorCommandArgumentsTransfer
    {
        $commandArgumentsTransfer = new IntegratorCommandArgumentsTransfer();

        $format = $input->getOption(static::OPTION_FORMAT);
        if ($format !== null) {
            $commandArgumentsTransfer->setFormat($format);
        }

        $isDry = (bool)$input->getOption(static::FLAG_DRY);
        $commandArgumentsTransfer->setIsDry($isDry);

        return $commandArgumentsTransfer;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string|null $format
     *
     * @return \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface
     */
    protected function createInputOutputAdapter(
        InputInterface $input,
        OutputInterface $output,
        ?string $format
    ): InputOutputInterface {
        $io = new SymfonyStyle($input, $output);

        if ($format === static::FORMAT_JSON) {
            return new SymfonyConsoleInputJsonOutputAdapter($io);
        }

        return new SymfonyConsoleInputOutputAdapter($io);
    }
}
