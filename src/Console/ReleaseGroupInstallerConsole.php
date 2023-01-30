<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReleaseGroupInstallerConsole extends AbstractInstallerConsole
{
    /**
     * @var string
     */
    protected const ARGUMENT_RELEASE_GROUP_ID = 'release-group-id';

    /**
     * @var string
     */
    protected const ARGUMENT_RELEASE_GROUP_IDS_DESCRIPTION = 'Id of release group, manifest of which should be executed';

    /**
     * @var string
     */
    protected const COMMAND_NAME = 'release-group:manifest:run';

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::COMMAND_NAME)
            ->setDescription('')
            ->addArgument(
                static::ARGUMENT_RELEASE_GROUP_ID,
                InputArgument::OPTIONAL,
                static::ARGUMENT_RELEASE_GROUP_IDS_DESCRIPTION,
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $releaseGroupIdList = $this->getReleaseGroupIdOrFail($input);
        $commandArgumentsTransfer = $this->buildCommandArgumentsTransfer($input);
        $io = $this->createInputOutputAdapter($input, $output, $commandArgumentsTransfer->getFormat());

        return $this->getFacade()->runReleaseGroupManifestInstallation(
            $releaseGroupIdList,
            $io,
            $commandArgumentsTransfer,
        );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    protected function getReleaseGroupIdOrFail(InputInterface $input): int
    {
        $argumentValue = $input->getArgument(static::ARGUMENT_RELEASE_GROUP_ID);
        if (!$argumentValue) {
            throw new InvalidArgumentException('Release group id is required');
        }
        if (!is_numeric($argumentValue)) {
            throw new InvalidArgumentException(sprintf('Invalid release group id `%s`', $argumentValue));
        }

        return (int)$argumentValue;
    }
}
