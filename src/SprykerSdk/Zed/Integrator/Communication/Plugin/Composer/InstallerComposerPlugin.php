<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Zed\Integrator\Communication\Plugin\Composer;

use Composer\Composer;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Generated\Shared\Transfer\ModuleTransfer;
use Generated\Shared\Transfer\OrganizationTransfer;
use Spryker\Service\UtilText\Model\Filter\SeparatorToCamelCase;
use Spryker\Zed\Console\Business\Model\Environment;
use Spryker\Zed\ModuleFinder\Business\ModuleFinderFacade;
use SprykerSdk\Zed\Integrator\Business\IntegratorFacade;
use SprykerSdk\Zed\Integrator\Business\IntegratorFacadeInterface;
use SprykerSdk\Zed\Integrator\Dependency\Console\ComposerInputOutputAdapter;

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
     * @var \Composer\DependencyResolver\Operation\OperationInterface[]
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
     * @return string[]
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
     * @api
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
     * @api
     *
     * @param \Composer\Script\Event $event
     *
     * @return void
     */
    public function runInstaller(Event $event): void
    {
        //Disable installer on update actions in travis
        if (getenv('TRAVIS')) {
            return;
        }

        $autoloadFile = $this->composer->getConfig()->get('vendor-dir') . '/autoload.php';
        include $autoloadFile;

        Environment::initialize();

        $updatedModules = $this->createModuleFinderFacade()->getModules();

        $this->getIntegratorFacade()->runInstallation($updatedModules, new ComposerInputOutputAdapter($this->io));
        $this->io->write('runInstallerEnd' . PHP_EOL);
    }

    /**
     * @param string $moduleName
     *
     * @return \Generated\Shared\Transfer\ModuleTransfer
     */
    protected function createModuleTransfer(string $moduleName): ModuleTransfer
    {
        $dashToCamelCaseFilter = new SeparatorToCamelCase();
        [$moduleName, $organisation] = explode('/', $moduleName);

        $organisationTransfer = (new OrganizationTransfer())
            ->setNameDashed($organisation)
            ->setName($dashToCamelCaseFilter->filter($organisation));

        $moduleTransfer = (new ModuleTransfer())
            ->setNameDashed($moduleName)
            ->setName($dashToCamelCaseFilter->filter($moduleName))
            ->setOrganization($organisationTransfer);

        return $moduleTransfer;
    }

    /**
     * @return \SprykerSdk\Zed\Integrator\Business\IntegratorFacadeInterface
     */
    protected function getIntegratorFacade(): IntegratorFacadeInterface
    {
        return new IntegratorFacade();
    }

    /**
     * @return \Spryker\Zed\ModuleFinder\Business\ModuleFinderFacade
     */
    protected function createModuleFinderFacade(): ModuleFinderFacade
    {
        return new ModuleFinderFacade();
    }
}
