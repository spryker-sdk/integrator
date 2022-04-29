<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use SprykerSdk\Integrator\IntegratorFacade;
use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
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
    /**
     * @var string
     */
    protected const MANIFESTS_DIR_PATH = '_data/manifests/src';

    /**
     * @var string
     */
    protected const ZIP_PATH = '_data/manifests/archive.zip';

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $zipPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::ZIP_PATH;
        $dirPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::MANIFESTS_DIR_PATH;

        parent::zipDir($dirPath, $zipPath);
    }

    /**
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        $fs = new Filesystem();
        $zipPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::ZIP_PATH;
        $fs->remove($zipPath);
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->prepareTestEnv();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->clearTestEnv();
    }

    /**
     * @return void
     */
    public function testRunInstallationWirePlugin(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

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
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorUnwirePlugin'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_unwire_plugin_dependency_provider.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorUnwirePlugin/TestIntegratorUnwirePluginDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationConfigureModule(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorConfigureModule'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_configure_module.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorDefault/TestIntegratorDefaultConfig.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationCopyModuleFile(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorCopyModuleFile'), $ioAdapter, false);

        // Assert
        $filePath = './tests/tmp/data/import_test.csv';
        $this->assertFileExists($filePath);
    }

    /**
     * @return void
     */
    public function testRunInstallationWireWidget(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorWireWidget'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_wire_widget.php';
        $classPath = './tests/tmp/src/Pyz/Yves/ShopApplication/ShopApplicationDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationUnwireWidget(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorUnwireWidget'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_unwire_widget.php';
        $classPath = './tests/tmp/src/Pyz/Yves/ShopApplication/ShopApplicationDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationConfigureEnv(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorConfigureEnv'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_configure_env.php';
        $classPath = './tests/tmp/config/Shared/config_default.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationConfigureEnvChoices(): void
    {
        // Arrange
        $fileSystem = $this->createFilesystem();
        if ($fileSystem->exists($this->getTempDirectoryPath())) {
            $fileSystem->copy('./tests/_data/project_mock/config/Shared/config_default.php', './tests/tmp/config/Shared/config_default.php');
            $fileSystem->copy('./tests/_data/project_mock/composer.json', './tests/tmp/composer.json');
            $fileSystem->copy('./tests/_data/project_mock/composer.lock', './tests/tmp/composer.lock');
        }

        $ioAdapter = $this->createMockSymfonyConsoleChoiceInputOutput('Value choice 1');

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorConfigureEnv'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_configure_env_choices.php';
        $classPath = './tests/tmp/config/Shared/config_default.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @param string $value
     *
     * @return \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface
     */
    public function createMockSymfonyConsoleChoiceInputOutput(string $value): InputOutputInterface
    {
        $ioAdapterMock = $this->createMock(SymfonyConsoleInputOutputAdapter::class);

        $ioAdapterMock->method('choice')
            ->willReturn($value);
        $ioAdapterMock->method('confirm')
            ->willReturn(true);

        return $ioAdapterMock;
    }

    /**
     * @return void
     */
    public function testRunInstallationWireGlueRelaitonship(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorWireGlueRelationship'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_wire_glue_relationship.php';
        $classPath = './tests/tmp/src/Pyz/Glue/GlueApplication/GlueApplicationDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationUnwireGlueRelaitonship(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorUnwireGlueRelationship'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_unwire_glue_relationship.php';
        $classPath = './tests/tmp/src/Pyz/Glue/GlueApplication/GlueApplicationDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationWireNavigationPlugin(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation($this->getModuleList('TestIntegratorWireNavigation'), $ioAdapter, false);

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_wire_navigation.xml';
        $resultFilePath = './tests/tmp/config/Zed/navigation.xml';

        $this->assertFileExists($resultFilePath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($resultFilePath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationAddConfigArrayElement(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runInstallation(
            $this->getModuleList('TestIntegratorAddConfigArrayElement'),
            $ioAdapter,
            false,
        );

        // Assert
        $testFilePath = './tests/_tests_files/test_integrator_add_config_array_element_config.php';
        $classPath = './tests/tmp/src/Pyz/Client/TestIntegratorAddConfigArrayElement/TestIntegratorAddConfigArrayElementConfig.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationGlossary(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()
            ->runInstallation($this->getModuleList('TestIntegratorGlossary'), $ioAdapter, false);

        // Assert
        $projectGlossaryFilePath = './tests/_data/project_mock/data/import/common/common/glossary.csv';
        $testFilePath = './tests/_tests_files/test_integrator_glossary.csv';
        $testResultFile = './tests/tmp/data/import/common/common/glossary.csv';

        $this->assertFileExists($testFilePath);
        $this->assertFileExists($testResultFile);
        $this->assertStringContainsString(trim(file_get_contents($projectGlossaryFilePath)), trim(file_get_contents($testResultFile)));
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($testResultFile)));
    }

    /**
     * @return \SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter
     */
    private function buildSymfonyConsoleInputOutputAdapter(): SymfonyConsoleInputOutputAdapter
    {
        $io = new SymfonyStyle($this->buildInput(), $this->buildOutput());
        $ioAdapter = new SymfonyConsoleInputOutputAdapter($io);
        $ioAdapter->setNoIteration();

        return $ioAdapter;
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
     *
     * @return array
     */
    private function getModuleList(?string $moduleName = null): array
    {
        return $this->getFactory()->getModuleFinderFacade()->getModules($this->buildModuleFilterTransfer($moduleName));
    }

    /**
     * @param string|null $moduleName
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer
     */
    private function buildModuleFilterTransfer(?string $moduleName = null): ModuleFilterTransfer
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
    private function createTmpStandaloneModulesDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $path = $this->getTempStandaloneModulesDirectoryPath();

        if (!$fileSystem->exists($path)) {
            $fileSystem->mkdir($path, 0700);
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
        $this->createTmpStandaloneModulesDirectory();
        $this->copyProjectMockToTmpDirectory();
    }

    /**
     * @return void
     */
    private function clearTestEnv(): void
    {
        //$this->removeTmpDirectory();
    }
}
