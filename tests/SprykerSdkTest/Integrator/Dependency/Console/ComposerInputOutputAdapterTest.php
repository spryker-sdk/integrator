<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Dependency\Console;

use Composer\IO\NullIO;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Dependency\Console\ComposerInputOutputAdapter;

class ComposerInputOutputAdapterTest extends TestCase
{
    /**
     * @var \SprykerSdk\Integrator\Dependency\Console\ComposerInputOutputAdapter
     */
    protected ComposerInputOutputAdapter $adapter;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->adapter = new ComposerInputOutputAdapter(new NullIO());
    }

    /**
     * @return void
     */
    public function testWrite(): void
    {
        $this->expectOutputString('');
        $this->adapter->write('Test message');
    }

    /**
     * @return void
     */
    public function testWriteln(): void
    {
        $this->expectOutputString('');
        $this->adapter->writeln('Test message');
    }

    /**
     * @return void
     */
    public function testAsk(): void
    {
        $this->assertSame('Default answer', $this->adapter->ask('Test question', 'Default answer'));

        $this->adapter->setIterationMode(false);
        $this->assertSame('Default answer', $this->adapter->ask('Test question', 'Default answer'));
    }

    /**
     * @return void
     */
    public function testConfirm(): void
    {
        $this->assertTrue($this->adapter->confirm('Test question', true));

        $this->adapter->setIterationMode(false);
        $this->assertFalse($this->adapter->confirm('Test question', false));
    }

    /**
     * @return void
     */
    public function testChoice(): void
    {
        $this->assertSame('Default', $this->adapter->choice('Test question', ['Test choice 1', 'Test choice 2'], 'Default'));

        $this->adapter->setNoIteration();
        $this->assertSame(1, $this->adapter->choice('Test question', ['Test choice 1', 'Test choice 2'], 1));
    }
}
