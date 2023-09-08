<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Console;

use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleInstallerConsole extends AbstractInstallerConsole
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'module:manifest:run';

    /**
     * @var string
     */
    public const ARGUMENT_MODULES = 'modules';

    /**
     * @var string
     */
    public const OPTION_SOURCE = 'source';

    /**
     * @var string
     */
    protected const ARGUMENT_MODULES_DESCRIPTION = 'Name of modules with versions (<module><:?version>) which should be built, separated by `,`.';

    /**
     * @var string
     */
    protected const OPTION_SOURCE_DESCRIPTION = 'Source branch of the manifests to be applied';

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::COMMAND_NAME)
            ->setDescription('')
            ->addArgument(
                static::ARGUMENT_MODULES,
                InputArgument::OPTIONAL,
                static::ARGUMENT_MODULES_DESCRIPTION,
            )
            ->addOption(
                static::OPTION_SOURCE,
                null,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_SOURCE_DESCRIPTION,
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
        $commandArgumentsTransfer = $this->buildCommandArgumentsTransfer($input);
        $io = $this->createInputOutputAdapter($input, $output, $commandArgumentsTransfer->getFormat());
        $this->getFacade()->runModuleManifestInstallation($io, $commandArgumentsTransfer);

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer
     */
    protected function buildCommandArgumentsTransfer(InputInterface $input): IntegratorCommandArgumentsTransfer
    {
        $commandArgumentsTransfer = parent::buildCommandArgumentsTransfer($input);

        $moduleArguments = (string)$input->getArgument(static::ARGUMENT_MODULES);

        if ($moduleArguments) {
            $moduleArguments = array_map('trim', explode(',', $moduleArguments));
            foreach ($moduleArguments as $moduleArgument) {
                $moduleParts = explode(':', $moduleArgument);
                $version = $moduleParts[1] ?? null;

                [$organization, $module] = explode('.', $moduleParts[0]);

                $commandArgumentsTransfer->addModule(
                    (new ModuleTransfer())
                        ->setModule($module)
                        ->setOrganization($organization)
                        ->setVersion($version),
                );
            }
        }

        $source = $input->getOption(static::OPTION_SOURCE);
        if ($source !== null) {
            $commandArgumentsTransfer->setSource($source);
        }

        return $commandArgumentsTransfer;
    }
}
