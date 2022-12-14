<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Plugin\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use SprykerSdk\Integrator\Common\UtilText\TextCaseHelper;
use SprykerSdk\Integrator\Dependency\Console\ComposerInputOutputAdapter;
use SprykerSdk\Integrator\IntegratorFacade;
use SprykerSdk\Integrator\IntegratorFacadeInterface;
use SprykerSdk\Integrator\ModuleFinder\ModuleFinderFacade;
use SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use SprykerSdk\Integrator\Transfer\OrganizationTransfer;

class InstallerComposerPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    /**
     * @var array<\Composer\DependencyResolver\Operation\OperationInterface>
     */
    protected $operations = [];

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        defined('APPLICATION_ROOT_DIR')
        || define('APPLICATION_ROOT_DIR', realpath($composer->getConfig()->get('vendor-dir') . '/..'));

        defined('APPLICATION_SOURCE_DIR')
        || define('APPLICATION_SOURCE_DIR', APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'src');

        defined('APPLICATION_VENDOR_DIR')
        || define('APPLICATION_VENDOR_DIR', APPLICATION_ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor');
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     *
     * @return void
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Composer\Composer $composer
     * @param \Composer\IO\IOInterface $io
     *
     * @return void
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<string>
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_UPDATE => 'collect',
            PackageEvents::POST_PACKAGE_UNINSTALL => 'collect',
            ScriptEvents::POST_UPDATE_CMD => 'runInstaller',
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @param \Composer\Installer\PackageEvent $event
     *
     * @return void
     */
    public function collect(PackageEvent $event): void
    {
        $this->operations[] = $event->getOperation();
    }

    /**
     * {@inheritDoc}
     *
     * @param \Composer\Script\Event $event
     *
     * @return void
     */
    public function runInstaller(Event $event): void
    {
        //Disable installer on shop pre-install phase
        if (!class_exists(ModuleTransfer::class)) {
            return;
        }

        $autoloadFile = $this->composer->getConfig()->get('vendor-dir') . '/autoload.php';
        include $autoloadFile;

        $updatedModules = $this->createModuleFinderFacade()->getModules();
        $commandArgumentsTransfer = (new IntegratorCommandArgumentsTransfer())
            ->setIsDry(false);

        $this->getIntegratorFacade()->runInstallation($updatedModules, new ComposerInputOutputAdapter($this->io), $commandArgumentsTransfer);
        $this->io->write('runInstallerEnd' . PHP_EOL);
    }

    /**
     * @param string $moduleName
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleTransfer
     */
    protected function createModuleTransfer(string $moduleName): ModuleTransfer
    {
        [$moduleName, $organization] = explode('/', $moduleName);

        $organisationTransfer = (new OrganizationTransfer())
            ->setNameDashed($organization)
            ->setName(TextCaseHelper::dashToCamelCase($organization, false));

        return (new ModuleTransfer())
            ->setNameDashed($moduleName)
            ->setName(TextCaseHelper::dashToCamelCase($moduleName, false))
            ->setOrganization($organisationTransfer);
    }

    /**
     * @return \SprykerSdk\Integrator\IntegratorFacadeInterface
     */
    protected function getIntegratorFacade(): IntegratorFacadeInterface
    {
        return new IntegratorFacade();
    }

    /**
     * @return \SprykerSdk\Integrator\ModuleFinder\ModuleFinderFacade
     */
    protected function createModuleFinderFacade(): ModuleFinderFacade
    {
        return new ModuleFinderFacade();
    }
}
