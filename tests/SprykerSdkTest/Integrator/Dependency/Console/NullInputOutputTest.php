<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator\Dependency\Console;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Dependency\Console\NullInputOutput;

class NullInputOutputTest extends TestCase
{
    /**
     * @var \SprykerSdk\Integrator\Dependency\Console\NullInputOutput
     */
    protected NullInputOutput $command;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->command = new NullInputOutput();
    }

    /**
     * @return void
     */
    public function testWrite(): void
    {
        $this->expectOutputString('Output message');
        $this->command->write('Output message');

        $this->expectOutputString('Output messageOutput message 2<br>Output message 3<br>');
        $this->command->write(
            [
                'Output message 2',
                'Output message 3',
            ],
            true,
        );
    }

    /**
     * @return void
     */
    public function testWriteln(): void
    {
        $this->expectOutputString('Output message 1<br>Output message 2<br><br>');
        $this->command->writeln(
            [
                'Output message 1',
                'Output message 2',
            ],
        );
    }

    /**
     * @return void
     */
    public function testAsk(): void
    {
        $this->assertSame(
            'Default answer',
            $this->command->ask('Test question here', 'Default answer'),
        );
    }

    /**
     * @return void
     */
    public function testConfirm(): void
    {
        $this->assertFalse($this->command->confirm('Test question here', false));
    }

    /**
     * @return void
     */
    public function testChoice(): void
    {
        $this->assertFalse($this->command->choice('Test question here', ['Option 1', 'Option 2'], false));
    }
}
