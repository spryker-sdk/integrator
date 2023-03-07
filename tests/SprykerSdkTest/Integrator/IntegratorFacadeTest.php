<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use SprykerSdk\Integrator\IntegratorFacade;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group test1
 */
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
    public function testRunInstallationConfigureModule(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorConfigureModule'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_configure_module.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorDefault/TestIntegratorDefaultConfig.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @group test2
     *
     * @return void
     */
    public function testRunInstallationWirePlugin(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorWirePlugin'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_wire_plugin_dependency_provider.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorWirePlugin/TestIntegratorWirePluginDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationUnwirePlugin(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorUnwirePlugin'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_unwire_plugin_dependency_provider.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorUnwirePlugin/TestIntegratorUnwirePluginDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationWireConsole(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorWireConsole'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_wire_console.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorWireConsoleCommands/ConsoleDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationUnwireConsole(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorUnwireConsole'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_unwire_console.php';
        $classPath = './tests/tmp/src/Pyz/Zed/TestIntegratorUnwireConsoleCommands/ConsoleDependencyProvider.php';

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
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorCopyModuleFile'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

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
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorWireWidget'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_wire_widget.php';
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
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorUnwireWidget'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_unwire_widget.php';
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
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorConfigureEnv'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_configure_env.php';
        $classPath = './tests/tmp/config/Shared/config_default.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
        $this->assertDuplicatedTargetDoesNotExistInFile(
            '\Pyz\Client\TestIntegratorAddConfigArrayElement\TestIntegratorAddConfigArrayElementConfig::TEST_VALUE_CHANGING',
            'Changed val',
            $classPath,
        );
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
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorConfigureEnv'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_configure_env_choices.php';
        $classPath = './tests/tmp/config/Shared/config_default.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));

        $this->assertDuplicatedTargetDoesNotExistInFile(
            '\Pyz\Client\TestIntegratorAddConfigArrayElement\TestIntegratorAddConfigArrayElementConfig::TEST_VALUE_CHANGING',
            'Changed val',
            $classPath,
        );
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
    public function testRunInstallationWireGlueRelationship(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorWireGlueRelationship'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_wire_glue_relationship.php';
        $classPath = './tests/tmp/src/Pyz/Glue/GlueApplication/GlueApplicationDependencyProvider.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationUnwireGlueRelationship(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorUnwireGlueRelationship'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_unwire_glue_relationship.php';
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
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorWireNavigation'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_wire_navigation.xml';
        $resultFilePath = './tests/tmp/config/Zed/navigation.xml';

        $this->assertFileExists($resultFilePath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($resultFilePath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationUnwireNavigationPlugin(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorUnwireNavigation'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_unwire_navigation.xml';
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
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorAddConfigArrayElement'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $testFilePath = './tests/_data/tests_files/test_integrator_add_config_array_element_config.php';
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
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $this->getModuleList('TestIntegratorGlossary'),
            $ioAdapter,
            $this->createCommandArgumentsTransfer(),
        );

        // Assert
        $projectGlossaryFilePath = './tests/_data/project_mock/data/import/common/common/glossary.csv';
        $testFilePath = './tests/_data/tests_files/test_integrator_glossary.csv';
        $testResultFile = './tests/tmp/data/import/common/common/glossary.csv';

        $this->assertFileExists($testFilePath);
        $this->assertFileExists($testResultFile);
        $this->assertStringContainsString(trim(file_get_contents($projectGlossaryFilePath)), trim(file_get_contents($testResultFile)));
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($testResultFile)));
    }

    /**
     * @param string $target
     * @param string $value
     * @param string $filePath
     *
     * @return void
     */
    private function assertDuplicatedTargetDoesNotExistInFile(string $target, string $value, string $filePath): void
    {
        $this->assertFalse(
            mb_strpos(
                trim(file_get_contents($filePath)),
                '$config[' . $target . '] = \'' . $value . '\';',
            ),
        );
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
        $this->removeTmpDirectory();
    }
}
