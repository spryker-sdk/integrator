<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Console;

use ReflectionClass;
use SprykerSdk\Integrator\Console\ModuleInstallerConsole;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputJsonOutputAdapter;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputOutputAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleInstallerConsoleTest extends KernelTestCase
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
     * @param $object
     * @param $methodName
     * @param array $parameters
     *
     * @return mixed
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
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
        $verboseOption = new InputOption('verboseOption', null, InputOutputInterface::DEBUG);
        $inputDefinition = new InputDefinition();

        $inputDefinition->addOption($verboseOption);

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
