<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Console;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SprykerSdk\Integrator\Console\DiffGenerateConsole;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class DiffGenerateConsoleTest extends TestCase
{
    /**
     * @return void
     */
    public function testBuildCommandArgumentsTransfer(): void
    {
        // Arrange
        $verboseOption = new InputOption('verboseOption', null, InputOutputInterface::DEBUG);
        $inputDefinition = new InputDefinition();
        $inputDefinition->addOption($verboseOption);
        $console = new DiffGenerateConsole();
        $arguments = [
            'branch-to-compare' => 'test1',
            'integration-branch' => 'test2',
            'diff-file-name' => 'test3',
            'release-group-id' => 10,
        ];
        $options = [
            'format' => 'yaml',
            'dry' => true,
        ];
        foreach ($options as $key => $value) {
            $option = (new InputOption($key, null, InputOption::VALUE_OPTIONAL));
            $option->setDefault($value);
            $inputDefinition->addOption($option);
        }
        foreach ($arguments as $key => $value) {
            $arg = (new InputArgument($key));
            $arg->setDefault($value);
            $inputDefinition->addArgument($arg);
        }
        $input = new ArrayInput([], $inputDefinition);

        // Act
        /** @var \SprykerSdk\Integrator\Transfer\IntegratorCommandArgumentsTransfer $integratorCommandArgumentsTransfer */
        $integratorCommandArgumentsTransfer = $this->invokeMethod(
            $console,
            'buildCommandArgumentsTransfer',
            [
                $input, $this->buildOutput(), null,
            ],
        );

        // Assert
        $this->assertSame('test1', $integratorCommandArgumentsTransfer->getBranchToCompare());
        $this->assertSame('test2', $integratorCommandArgumentsTransfer->getIntegrationBranch());
        $this->assertSame('test3', $integratorCommandArgumentsTransfer->getDiffFileName());
        $this->assertSame(10, $integratorCommandArgumentsTransfer->getReleaseGroupId());
        $this->assertSame('yaml', $integratorCommandArgumentsTransfer->getFormat());
        $this->assertTrue($integratorCommandArgumentsTransfer->getIsDry());
    }

    /**
     * @return void
     */
    public function testGetReleaseGroupSuccess(): void
    {
        // Arrange
        $releaseGroupIdArgument = new InputArgument('release-group-id');
        $releaseGroupIdArgument->setDefault(10);
        $console = new DiffGenerateConsole();

        // Act
        $releaseGroupId = $this->invokeMethod(
            $console,
            'getReleaseGroupIdOrFail',
            [
                $this->buildInput($releaseGroupIdArgument), $this->buildOutput(), null,
            ],
        );

        // Assert
        $this->assertSame(10, $releaseGroupId);
    }

    /**
     * @return void
     */
    public function testGetReleaseGroupFailedNoArgument(): void
    {
        // Arrange
        $console = new DiffGenerateConsole();

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $releaseGroupId = $this->invokeMethod(
            $console,
            'getReleaseGroupIdOrFail',
            [
                $this->buildInput(), $this->buildOutput(), null,
            ],
        );
    }

    /**
     * @return void
     */
    public function testGetReleaseGroupFailedInvalidArgumentType(): void
    {
        // Arrange
        $releaseGroupIdArgument = new InputArgument('release-group-id');
        $releaseGroupIdArgument->setDefault('invalid-id');
        $console = new DiffGenerateConsole();

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $releaseGroupId = $this->invokeMethod(
            $console,
            'getReleaseGroupIdOrFail',
            [
                $this->buildInput($releaseGroupIdArgument), $this->buildOutput(), null,
            ],
        );
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
     * @param \Symfony\Component\Console\Input\InputArgument|null $releaseGroupIdArgument
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    private function buildInput(?InputArgument $releaseGroupIdArgument = null): InputInterface
    {
        $verboseOption = new InputOption('verboseOption', null, InputOutputInterface::DEBUG);
        $inputDefinition = new InputDefinition();

        $inputDefinition->addOption($verboseOption);
        if ($releaseGroupIdArgument) {
            $inputDefinition->addArgument($releaseGroupIdArgument);
        }

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
