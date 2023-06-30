<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator;

use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use Symfony\Component\Filesystem\Filesystem;

class IntegratorFacadeTest extends AbstractIntegratorFacade
{
    /**
     * @var string
     */
    protected const MANIFESTS_DIR_PATH = 'integrator/manifests/src';

    /**
     * @var string
     */
    protected const ZIP_PATH = '_data/archive.zip';

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $zipPath = ROOT_TESTS . DIRECTORY_SEPARATOR . static::ZIP_PATH;
        $dirPath = DATA_PROVIDER_DIR . DIRECTORY_SEPARATOR . static::MANIFESTS_DIR_PATH;

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
    public function testRunInstallationConfigureModule(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorConfigureModule')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/src/Pyz/Zed/TestIntegratorConfigureModule/TestIntegratorDefaultConfig.php';
        $classPath = $this->getTestTmpDirPath() . '/src/Pyz/Zed/TestIntegratorDefault/TestIntegratorDefaultConfig.php';

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);

        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
    }

    /**
     * @return void
     */
    public function testRunInstallationWirePlugin(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorWirePlugin')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/src/Pyz/Zed/TestIntegratorWirePlugin/TestIntegratorWirePluginDependencyProvider.php';
        $classPath = $this->getTestTmpDirPath() . '/src/Pyz/Zed/TestIntegratorWirePlugin/TestIntegratorWirePluginDependencyProvider.php';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorUnwirePlugin')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/src/Pyz/Zed/TestIntegratorUnwirePlugin/TestIntegratorUnwirePluginDependencyProvider.php';
        $classPath = $this->getTestTmpDirPath() . '/src/Pyz/Zed/TestIntegratorUnwirePlugin/TestIntegratorUnwirePluginDependencyProvider.php';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorWireConsole')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/src/Pyz/Zed/TestIntegratorWireConsoleCommands/ConsoleDependencyProvider.php';
        $classPath = $this->getTestTmpDirPath() . '/src/Pyz/Zed/TestIntegratorWireConsoleCommands/ConsoleDependencyProvider.php';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorUnwireConsole')]),
        );

        // Assert
        $consoleDependencyProviderPath = '/src/Pyz/Zed/TestIntegratorUnwireConsoleCommands/ConsoleDependencyProvider.php';
        $testFilePath = $this->getProjectMockCurrentPath() . $consoleDependencyProviderPath;
        $classPath = $this->getTestTmpDirPath() . $consoleDependencyProviderPath;

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorCopyModuleFile')]),
        );

        // Assert
        $filePath = $this->getTestTmpDirPath() . '/data/import_test.csv';
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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorWireWidget')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/src/Pyz/Yves/ShopApplication/TestIntegratorWireWidget/ShopApplicationDependencyProvider.php';
        $classPath = $this->getTestTmpDirPath() . '/src/Pyz/Yves/ShopApplication/TestIntegratorWireWidget/ShopApplicationDependencyProvider.php';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorUnwireWidget')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/src/Pyz/Yves/ShopApplication/TestIntegratorUnwireWidget/ShopApplicationDependencyProvider.php';
        $classPath = $this->getTestTmpDirPath() . '/src/Pyz/Yves/ShopApplication/TestIntegratorUnwireWidget/ShopApplicationDependencyProvider.php';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorConfigureEnv')]),
        );

        // Assert
        $configPath = '/config/Shared/config_default.php';
        $testFilePath = $this->getProjectMockCurrentPath() . $configPath;
        $classPath = $this->getTestTmpDirPath() . $configPath;

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
            $fileSystem->copy($this->getProjectMockOriginalPath() . '/config/Shared/config_default.php', $this->getTestTmpDirPath() . '/config/Shared/config_default.php');
            $fileSystem->copy($this->getProjectMockOriginalPath() . '/composer.json', $this->getTestTmpDirPath() . '/composer.json');
            $fileSystem->copy($this->getProjectMockOriginalPath() . '/composer.lock', $this->getTestTmpDirPath() . '/composer.lock');
        }

        $ioAdapter = $this->createMockSymfonyConsoleChoiceInputOutput('Value choice 1');

        // Act
        $this->createIntegratorFacade()->runModuleManifestInstallation(
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorConfigureEnv')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/config/Shared/config_default_choices.php';
        $classPath = $this->getTestTmpDirPath() . '/config/Shared/config_default.php';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorWireGlueRelationship')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/src/Pyz/Glue/GlueApplication/TestIntegratorWireGlueRelationship/GlueApplicationDependencyProvider.php';
        $classPath = $this->getTestTmpDirPath() . '/src/Pyz/Glue/GlueApplication/TestIntegratorWireGlueRelationship/GlueApplicationDependencyProvider.php';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorUnwireGlueRelationship')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/src/Pyz/Glue/GlueApplication/TestIntegratorUnwireGlueRelationship/GlueApplicationDependencyProvider.php';
        $classPath = $this->getTestTmpDirPath() . '/src/Pyz/Glue/GlueApplication/TestIntegratorUnwireGlueRelationship/GlueApplicationDependencyProvider.php';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorWireNavigation')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/config/Zed/TestIntegratorWireNavigation/navigation.xml';
        $resultFilePath = $this->getTestTmpDirPath() . '/config/Zed/navigation.xml';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorUnwireNavigation')]),
        );

        // Assert
        $testFilePath = $this->getProjectMockCurrentPath() . '/config/Zed/TestIntegratorUnwireNavigation/navigation.xml';
        $resultFilePath = $this->getTestTmpDirPath() . '/config/Zed/navigation.xml';

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
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorAddConfigArrayElement')]),
        );

        // Assert
        $testConfig = '/src/Pyz/Client/TestIntegratorAddConfigArrayElement/TestIntegratorAddConfigArrayElementConfig.php';
        $testFilePath = $this->getProjectMockCurrentPath() . $testConfig;
        $classPath = $this->getTestTmpDirPath() . $testConfig;

        $this->assertFileExists($classPath);
        $this->assertFileExists($testFilePath);
        $this->assertSame(trim(file_get_contents($testFilePath)), trim(file_get_contents($classPath)));
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
     * @return void
     */
    public function testRunUpdateLock(): void
    {
        // Arrange
        $ioAdapter = $this->buildSymfonyConsoleInputOutputAdapter();

        // Act
        $this->createIntegratorFacade()->runUpdateLock(
            $ioAdapter,
            $this->createCommandArgumentsTransfer(false, [$this->getModuleTransfer('Spryker.TestIntegratorConfigureModule')]),
        );

        // Assert
        $integratorLock = $this->getTestTmpDirPath() . '/integrator.lock';
        $this->assertFileExists($integratorLock);

        $this->assertNotEmpty(trim(file_get_contents($integratorLock)));
    }

    /**
     * @return void
     */
    protected function copyProjectMockToTmpDirectory(): void
    {
        $fileSystem = $this->createFilesystem();
        $tmpPath = $this->getTempDirectoryPath();
        $projectMockPath = $this->getProjectMockOriginalPath();

        if ($fileSystem->exists($this->getTempDirectoryPath())) {
            $fileSystem->mirror($projectMockPath, $tmpPath);
        }
    }
}
