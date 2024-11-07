<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\ConfigurationEnvironmentBuilder;

use Generator;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\StringConfigurationEnvironmentStrategy;

class StringConfigurationEnvironmentStrategyTest extends TestCase
{
    /**
     * @var \SprykerSdk\Integrator\Builder\ConfigurationEnvironmentBuilder\StringConfigurationEnvironmentStrategy
     */
    protected StringConfigurationEnvironmentStrategy $strategy;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->strategy = new StringConfigurationEnvironmentStrategy();
    }

    /**
     * @return \Generator
     */
    public static function isApplicableDataProvider(): Generator
    {
        yield [false, []];
        yield [false, 12345];
        yield [false, 'test string'];
        yield [false, 'test 125125-)(*&^%'];
        yield [true, '\'test 125125-)(*&^%\''];
        yield [true, "'test 125125-)(*&^%'"];
    }

    /**
     * @dataProvider isApplicableDataProvider
     *
     * @param bool $expRes
     * @param mixed $value
     *
     * @return void
     */
    public function testIsApplicable(bool $expRes, $value): void
    {
        $this->assertSame($expRes, $this->strategy->isApplicable($value));
    }

    /**
     * @return \Generator
     */
    public static function getFormattedExpressionDataProvider(): Generator
    {
        yield ['\'       lorem ipsum\'', "       lorem ipsum'''''''''''''''"];
        yield ['\'lorem ipsum\'', 'lorem ipsum'];
    }

    /**
     * @dataProvider getFormattedExpressionDataProvider
     *
     * @param string $expRes
     * @param string $value
     *
     * @return void
     */
    public function testGetFormattedExpression(string $expRes, string $value): void
    {
        $this->assertSame($expRes, $this->strategy->getFormattedExpression($value));
    }
}
