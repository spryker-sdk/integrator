<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator\Business;

use SprykerSdk\Integrator\Business\IntegratorFacade;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use SprykerSdk\Shared\Transfer\ModuleFilterTransfer;
use SprykerSdk\Shared\Transfer\ModuleTransfer;
use SprykerSdkTest\Integrator\BaseTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class IntegratorFacadeTest extends BaseTestCase
{
    protected const RECIPES_DIR_PATH = '_data/recipes/src';
    protected const ZIP_PATH = '_data/recipes/archive.zip';

    public static function setUpBeforeClass(): void
    {
        echo 'setUpBeforeClass'.PHP_EOL;

        $zipPath = ROOT_TESTS.DIRECTORY_SEPARATOR.static::ZIP_PATH;
        $dirPath = ROOT_TESTS.DIRECTORY_SEPARATOR.static::RECIPES_DIR_PATH;

        parent::zipDir($dirPath, $zipPath);
    }


    public static function tearDownAfterClass(): void
    {
        $fs = new Filesystem();
        $zipPath = ROOT_TESTS.DIRECTORY_SEPARATOR.static::ZIP_PATH;
        $fs->remove($zipPath);
    }

    protected function setUp(): void
    {
         $this->prepareTestEnv();
    }

    protected function tearDown(): void
    {
        $this->clearTestEnv(); // GEEGA remove it
    }


    /**
     * @return void
     */
    public function testRunInstallationWirePlugin(): void
    {
        // Arrange
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorWirePlugin'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_wire_plugin_dependency_provider.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorWirePlugin/TestIntegratorWirePluginDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($classPath)), trim(file_get_contents($testFilePath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationUnwirePlugin(): void
    {
        // Arrange
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorUnwirePlugin'), $ioAdapter, false);


        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_unwire_plugin_dependency_provider.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorDefault/TestIntegratorDefaultDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($classPath)), trim(file_get_contents($testFilePath)));

    }


    public function testRunInstallationConfigureModule(): void
    {
        $this->markTestSkipped('Skip');

        // Arrange
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorConfigureModule'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_configure_module.php';
        $classPath = './tests/_data/project_mock/src/Pyz/Zed/TestIntegratorDefault/TestIntegratorDefaultConfig.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    public function testRunInstallationCopyModuleFile(): void
    {
        // Arrange
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorCopyModuleFile'), $ioAdapter, false);


        // Assert
        // TODO

    }

    public function testRunInstallationWireWidget(): void
    {
        $this->markTestSkipped('Skip');

        // Arrange
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorWireWidget'), $ioAdapter, false);

        // Assert
        // TODO
    }

    public function testRunInstallationUnwireWidget(): void
    {
        $this->markTestSkipped('Skip');

        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorUnwireWidget'), $ioAdapter, false);

        // Assert
        // TODO
    }

    public function testRunInstallationConfigureEnv(): void
    {
        $this->markTestSkipped('Skip');
        // Arrange
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorConfigureEnv'), $ioAdapter, false);


        // Assert
        // TODO
    }

    public function testRunInstallationWireGlueRelaitonship(): void
    {
         $this->markTestSkipped('Skip');

        // Arrange
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorWireGlueRelationship'), $ioAdapter, false);


        // Assert
        // TODO
    }

    public function testRunInstallationUnwireGlueRelaitonship(): void
    {
        $this->markTestSkipped('Skip');

        // Arrange
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorUnwireGlueRelationship'), $ioAdapter, false);


        // Assert
        // TODO
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    private function buildInput(): InputInterface
    {
        $verboseOption = new InputOption(InputOutputInterface::DEBUG);
        $inputDefinition = new InputDefinition();

        $inputDefinition->addOption($verboseOption);

        return new ArrayInput([], $inputDefinition);
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    private function buildOutput(): OutputInterface
    {
        return new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param string|null $moduleName
     * @return array
     */
    private function getModuleList(string $moduleName = null): array
    {
        return $this->getFactory()->getModuleFinderFacade()->getModules($this->buildModuleFilterTransfer($moduleName));
    }

    /**
     * @return \SprykerSdk\Shared\Transfer\ModuleFilterTransfer
     */
    private function buildModuleFilterTransfer(string  $moduleName = null): ModuleFilterTransfer
    {
        $moduleFilterTransfer = new ModuleFilterTransfer();

        if ($moduleName) {
            $moduleTransfer = new ModuleTransfer();
            $moduleTransfer->setName($moduleName);
            $moduleFilterTransfer->setModule($moduleTransfer);
        }

        return $moduleFilterTransfer;
    }

    /**
     * @return \SprykerSdk\Integrator\Business\IntegratorFacade
     */
    private function createIntegratorFacade(): IntegratorFacade
    {
        return new IntegratorFacade();
    }

    /**
     * @return void
     */
    private function createTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();

        if (!$fileSystem->exists($tmpPath)) {
            $fileSystem->mkdir($tmpPath, 0700);
        }
    }

    /**
     * @return void
     */
    private function removeTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();

        if ($fileSystem->exists($tmpPath)) {
            $fileSystem->remove($tmpPath);
        }
    }

    /**
     * @return void
     */
    private function copyProjectMockToTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();
        $projectMockPath = $this->getProjectMockPath();

        if ($fileSystem->exists($this->getTempDirectoryPath())) {
            $fileSystem->mirror($projectMockPath, $tmpPath);
        }
    }

    /**
     * @return void
     */
    private function prepareTestEnv(): void
    {
        $this->removeTmpDirectory();
        $this->createTmpDirectory();
        $this->copyProjectMockToTmpDirectory();
    }

    /**
     * @return void
     */
    private function clearTestEnv(): void
    {
        $this->removeTmpDirectory();
    }
}
