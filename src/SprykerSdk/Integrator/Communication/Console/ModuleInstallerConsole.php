<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Communication\Console;

use Shared\Transfer\ModuleFilterTransfer;
use Shared\Transfer\ModuleTransfer;
use Shared\Transfer\OrganizationTransfer;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use SprykerSdk\Shared\Integrator\IntegratorFactoryAwareTrait;
use SprykerSdk\Shared\Integrator\IntegratorFacadeAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ModuleInstallerConsole extends Command
{
    use IntegratorFactoryAwareTrait;
    use IntegratorFacadeAwareTrait;

    protected const ARGUMENT_MODULE_NAMES = 'module-names';
    protected const FLAG_DRY = 'dry';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('integrator:manifest:run')
            ->setDescription('')
            ->addOption(static::FLAG_DRY)
            ->addArgument(
                static::ARGUMENT_MODULE_NAMES,
                InputArgument::OPTIONAL,
                'Name of modules which should be build, separated by `,`'
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
        $io = new SymfonyStyle($input, $output);

        $moduleList = $this->getModuleList($input);
        $isDry = $this->getDryOptionValue($input);

        $this->getFacade()->runInstallation($moduleList, new SymfonyConsoleInputOutputAdapter($io), $isDry);

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Shared\Transfer\ModuleTransfer[]
     */
    protected function getModuleList(InputInterface $input): array
    {
        $moduleNames = (string)$input->getArgument(static::ARGUMENT_MODULE_NAMES);

        return $this->getFactory()->getModuleFinderFacade()->getModules($this->buildModuleFilterTransfer($moduleNames));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return bool
     */
    protected function getDryOptionValue(InputInterface $input): bool
    {
        return (bool)$input->getOption(static::FLAG_DRY);
    }

    /**
     * @param string $moduleArgument
     *
     * @return \Shared\Transfer\ModuleFilterTransfer|null
     */
    protected function buildModuleFilterTransfer(string $moduleArgument): ?ModuleFilterTransfer
    {
        $moduleFilterTransfer = new ModuleFilterTransfer();

        if (!$moduleArgument) {
            return $moduleFilterTransfer;
        }

        if (strpos($moduleArgument, '.') === false) {
            $moduleTransfer = new ModuleTransfer();
            $moduleTransfer->setName($moduleArgument);
            $moduleFilterTransfer->setModule($moduleTransfer);

            return $moduleFilterTransfer;
        }

        $this->addModuleFilterDetails($moduleArgument, $moduleFilterTransfer);

        return $moduleFilterTransfer;
    }

    /**
     * @param string $moduleArgument
     * @param \Shared\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     *
     * @return \Shared\Transfer\ModuleFilterTransfer
     */
    protected function addModuleFilterDetails(string $moduleArgument, ModuleFilterTransfer $moduleFilterTransfer): ModuleFilterTransfer
    {
        [$organization, $module] = explode('.', $moduleArgument);

        if ($module !== '*' && $module !== 'all') {
            $moduleTransfer = new ModuleTransfer();
            $moduleTransfer->setName($module);

            $moduleFilterTransfer->setModule($moduleTransfer);
        }

        $organizationTransfer = new OrganizationTransfer();
        $organizationTransfer->setName($organization);

        $moduleFilterTransfer->setOrganization($organizationTransfer);

        return $moduleFilterTransfer;
    }
}
