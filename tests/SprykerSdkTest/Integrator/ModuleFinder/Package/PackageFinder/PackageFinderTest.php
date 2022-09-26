<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\ModuleFinder\Package\PackageFinder;

use Generator;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\ModuleFinder\Package\PackageFinder\PackageFinder;

class PackageFinderTest extends TestCase
{
    /**
     * @var \SprykerSdk\Integrator\ModuleFinder\Package\PackageFinder\PackageFinder
     */
    protected PackageFinder $packageFinder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $configMock = $this->createMock(IntegratorConfig::class);
        $configMock->method('getVendorDirectory')->willReturn('./tests/_data/project_mock/vendor/');

        $this->packageFinder = new PackageFinder($configMock);
    }

    /**
     * @dataProvider camelCaseDataProvider
     *
     * @param string $expRes
     * @param string $inputStr
     *
     * @return void
     */
    public function testCamelCase(string $expRes, string $inputStr): void
    {
        $this->assertSame($expRes, $this->packageFinder->camelCase($inputStr));
    }

    /**
     * @return \Generator
     */
    public function camelCaseDataProvider(): Generator
    {
        $testData = [
            ['Test data here 1', 'test data here 1'],
            ['TestDataHere 2', 'test-data-here 2'],
            ['TestDataHere3', 'test-data-here-3'],
            ['Test_data_here 4', 'test_data_here 4'],
            ['TEsTdAtaHERe 5', 'tEsTdAtaHERe 5'],
        ];

        foreach ($testData as $set) {
            yield $set;
        }
    }

    /**
     * @return void
     */
    public function testGetPackages(): void
    {
        $this->assertSame([], $this->packageFinder->getPackages());
    }
}
