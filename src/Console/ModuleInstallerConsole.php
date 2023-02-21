<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Console;

use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use SprykerSdk\Integrator\Transfer\OrganizationTransfer;
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
    protected const ARGUMENT_MODULE_NAMES = 'module-names';

    /**
     * @var string
     */
    protected const ARGUMENT_MODULE_NAMES_DESCRIPTION = 'Name of modules which should be built, separated by `,`';

    /**
     * @var string
     */
    protected const OPTION_SOURCE = 'source';

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
                static::ARGUMENT_MODULE_NAMES,
                InputArgument::OPTIONAL,
                static::ARGUMENT_MODULE_NAMES_DESCRIPTION,
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
        $moduleList = $this->getModuleList($input);
        $commandArgumentsTransfer = $this->buildCommandArgumentsTransfer($input);
        $io = $this->createInputOutputAdapter($input, $output, $commandArgumentsTransfer->getFormat());
        $this->getFacade()->runModuleManifestInstallation($moduleList, $io, $commandArgumentsTransfer);

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<\SprykerSdk\Integrator\Transfer\ModuleTransfer>
     */
    protected function getModuleList(InputInterface $input): array
    {
        $moduleNames = (string)$input->getArgument(static::ARGUMENT_MODULE_NAMES);

        return $this->getFactory()->getModuleFinderFacade()->getModules($this->buildModuleFilterTransfer($moduleNames));
    }

    /**
     * @param string $moduleArgument
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer|null
     */
    protected function buildModuleFilterTransfer(string $moduleArgument): ?ModuleFilterTransfer
    {
        $moduleFilterTransfer = new ModuleFilterTransfer();

        if (!$moduleArgument) {
            return $moduleFilterTransfer;
        }

        if (!preg_match('/[a-zA-Z]+\.[a-zA-z]+/', $moduleArgument)) {
            $moduleName = $moduleArgument;
            $version = null;

            if (strpos($moduleArgument, ':') !== false) {
                [$moduleName, $version] = explode(':', $moduleArgument);
            }

            $moduleTransfer = new ModuleTransfer();
            $moduleTransfer->setName($moduleName)
                ->setVersion($version);

            $moduleFilterTransfer->setModule($moduleTransfer);

            return $moduleFilterTransfer;
        }

        $this->addModuleFilterDetails($moduleArgument, $moduleFilterTransfer);

        return $moduleFilterTransfer;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer
     */
    protected function buildCommandArgumentsTransfer(InputInterface $input): IntegratorCommandArgumentsTransfer
    {
        $commandArgumentsTransfer = parent::buildCommandArgumentsTransfer($input);

        $source = $input->getOption(static::OPTION_SOURCE);
        if ($source !== null) {
            $commandArgumentsTransfer->setSource($source);
        }

        return $commandArgumentsTransfer;
    }

    /**
     * @param string $moduleArgument
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer
     */
    protected function addModuleFilterDetails(string $moduleArgument, ModuleFilterTransfer $moduleFilterTransfer): ModuleFilterTransfer
    {
        $version = null;

        if (strpos($moduleArgument, ':') !== false) {
            [$moduleArgument, $version] = explode(':', $moduleArgument);
        }

        [$organization, $module] = explode('.', $moduleArgument);

        if ($module !== '*' && $module !== 'all') {
            $moduleTransfer = new ModuleTransfer();
            $moduleTransfer->setName($module)
                ->setVersion($version);

            $moduleFilterTransfer->setModule($moduleTransfer);
        }

        $organizationTransfer = new OrganizationTransfer();
        $organizationTransfer->setName($organization);

        $moduleFilterTransfer->setOrganization($organizationTransfer);

        return $moduleFilterTransfer;
    }
}
