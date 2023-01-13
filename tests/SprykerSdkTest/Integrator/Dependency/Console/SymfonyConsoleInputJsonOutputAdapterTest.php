<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Dependency\Console;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputJsonOutputAdapter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SymfonyConsoleInputJsonOutputAdapterTest extends TestCase
{
    /**
     * @return void
     */
    public function testWrite(): void
    {
        //Assert
        $this->expectOutputString('');

        //Arrange
        $output = $this->buildOutput();
        $ioAdapter = $this->buildInputOutputAdapter($output);

        //Act
        $ioAdapter->write('Test message');
        unset($ioAdapter);

        //Assert
        $this->assertSame('{"message-list":["Test message"],"warning-list":[]}', $output->fetch());
    }

    /**
     * @return void
     */
    public function testWriteln(): void
    {
        //Assert
        $this->expectOutputString('');

        //Arrange
        $output = $this->buildOutput();
        $ioAdapter = $this->buildInputOutputAdapter($output);

        //Act
        $ioAdapter->writeln('Test message');
        unset($ioAdapter);

        //Assert
        $this->assertSame('{"message-list":["Test message"],"warning-list":[]}', $output->fetch());
    }

    /**
     * @return void
     */
    public function testWarning(): void
    {
        //Assert
        $this->expectOutputString('');

        //Arrange
        $output = $this->buildOutput();
        $ioAdapter = $this->buildInputOutputAdapter($output);

        //Act
        $ioAdapter->warning('Test warning');
        unset($ioAdapter);

        //Assert
        $this->assertSame('{"message-list":[],"warning-list":["Test warning"]}', $output->fetch());
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \SprykerSdk\Integrator\Dependency\Console\SymfonyConsoleInputJsonOutputAdapter
     */
    private function buildInputOutputAdapter(OutputInterface $output): SymfonyConsoleInputJsonOutputAdapter
    {
        $io = new SymfonyStyle($this->buildInput(), $output);

        return new SymfonyConsoleInputJsonOutputAdapter($io);
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
