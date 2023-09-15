<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Console;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SprykerSdk\Integrator\Console\ModuleInstallerConsole;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputJsonOutputAdapter;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleInstallerConsoleTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateInputOutputAdapter(): void
    {
        //Arrange
        $console = new ModuleInstallerConsole();

        //Act
        $io = $this->invokeMethod(
            $console,
            'createInputOutputAdapter',
            [
                $this->buildInput(), $this->buildOutput(), null,
            ],
        );

        //Assert
        $this->assertInstanceOf(SymfonyConsoleInputOutputAdapter::class, $io);
    }

    /**
     * @return void
     */
    public function testCreateJsonInputOutputAdapter(): void
    {
        //Arrange
        $console = new ModuleInstallerConsole();

        //Act
        $io = $this->invokeMethod(
            $console,
            'createInputOutputAdapter',
            [
                $this->buildInput(), $this->buildOutput(), 'json',
            ],
        );

        //Assert
        $this->assertInstanceOf(SymfonyConsoleInputJsonOutputAdapter::class, $io);
    }

    /**
     * @return void
     */
    public function testExecuteTransferBuilderShouldBuildProperTransferWithVersions(): void
    {
        //Arrange
        $console = new ModuleInstallerConsole();

        $input = $this->buildInput();
        $input->setArgument(ModuleInstallerConsole::ARGUMENT_MODULES, 'Spryker.Acl:1.1.1,SprykerShop.Product');

        //Act
        /** @var \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $argumentsTransfer */
        $argumentsTransfer = $this->invokeMethod(
            $console,
            'buildCommandArgumentsTransfer',
            [
                $input, $this->buildOutput(), 'json',
            ],
        );

        //Assert
        $this->assertCount(2, $argumentsTransfer->getModules());

        $module = $argumentsTransfer->getModules()[0];
        $this->assertSame('Spryker', $module->getOrganization());
        $this->assertSame('Acl', $module->getModule());
        $this->assertSame('1.1.1', $module->getVersion());

        $module = $argumentsTransfer->getModules()[1];
        $this->assertSame('SprykerShop', $module->getOrganization());
        $this->assertSame('Product', $module->getModule());
        $this->assertNull($module->getVersion());
    }

    /**
     * @param mixed $object
     * @param string $methodName
     * @param array $parameters
     *
     * @return mixed
     */
    protected function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    private function buildInput(): InputInterface
    {
        $inputDefinition = new InputDefinition();

        $inputDefinition->addOption(
            new InputOption(ModuleInstallerConsole::OPTION_FORMAT, null, InputOption::VALUE_OPTIONAL),
        );
        $inputDefinition->addOption(
            new InputOption(ModuleInstallerConsole::OPTION_SOURCE, null, InputOption::VALUE_OPTIONAL),
        );
        $inputDefinition->addOption(
            new InputOption(ModuleInstallerConsole::FORMAT_JSON, null, InputOption::VALUE_OPTIONAL),
        );
        $inputDefinition->addOption(
            new InputOption(ModuleInstallerConsole::FLAG_DRY, null, InputOption::VALUE_OPTIONAL),
        );

        $inputDefinition->addArguments([new InputArgument(ModuleInstallerConsole::ARGUMENT_MODULES)]);

        return new ArrayInput([], $inputDefinition);
    }

    /**
     * @return \Symfony\Component\Console\Output\BufferedOutput
     */
    private function buildOutput(): BufferedOutput
    {
        return new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);
    }
}
