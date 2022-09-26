<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\ModuleFinder\Module\ModuleMatcher;

use Generator;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcher;
use SprykerSdk\Integrator\Transfer\ApplicationTransfer;
use SprykerSdk\Integrator\Transfer\ModuleFilterTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;
use SprykerSdk\Integrator\Transfer\OrganizationTransfer;

class ModuleMatcherTest extends TestCase
{
    /**
     * @var string
     */
    protected const CORRECT_NAME = 'Correct name';

    /**
     * @var string
     */
    protected const WRONG_NAME = 'Wrong name';

    /**
     * @var \SprykerSdk\Integrator\ModuleFinder\Module\ModuleMatcher\ModuleMatcher
     */
    protected ModuleMatcher $moduleMatcher;

    /**
     * @return \Generator
     */
    public function matchesDataProvider(): Generator
    {
        $applicationTransferGeneral = $this->createMock(ApplicationTransfer::class);
        $applicationTransferGeneral->method('getNameOrFail')->willReturn(static::CORRECT_NAME);

        $organizationTransferGeneral = $this->createMock(OrganizationTransfer::class);
        $organizationTransferGeneral->method('getNameOrFail')->willReturn(static::CORRECT_NAME);

        $moduleTransferGeneral = $this->createMock(ModuleTransfer::class);
        $moduleTransferGeneral->method('getNameOrFail')->willReturn(static::CORRECT_NAME);
        $moduleTransferGeneral->method('getOrganizationOrFail')->willReturn($organizationTransferGeneral);
        $moduleTransferGeneral->method('getApplications')->willReturn([
            $applicationTransferGeneral,
        ]);

        $dataSet = [
            [false, $moduleTransferGeneral, $this->createMockModuleFilterTransfer(false, true, true)],
            [false, $moduleTransferGeneral, $this->createMockModuleFilterTransfer(true, false, true)],
            [false, $moduleTransferGeneral, $this->createMockModuleFilterTransfer(true, true, false)],
            [true, $moduleTransferGeneral, $this->createMockModuleFilterTransfer(true, true, true)],
        ];

        foreach ($dataSet as $testData) {
            yield $testData;
        }
    }

    /**
     * @dataProvider matchesDataProvider
     *
     * @param bool $expResult
     * @param \SprykerSdk\Integrator\Transfer\ModuleTransfer $moduleTransfer
     * @param \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer $moduleFilterTransfer
     *
     * @return void
     */
    public function testMatches(bool $expResult, ModuleTransfer $moduleTransfer, ModuleFilterTransfer $moduleFilterTransfer): void
    {
        $this->assertSame($expResult, $this->moduleMatcher->matches($moduleTransfer, $moduleFilterTransfer));
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->moduleMatcher = new ModuleMatcher();
    }

    /**
     * @param bool $matchOrganization
     * @param bool $matchApplication
     * @param bool $matchModule
     *
     * @return \SprykerSdk\Integrator\Transfer\ModuleFilterTransfer
     */
    protected function createMockModuleFilterTransfer(
        bool $matchOrganization,
        bool $matchApplication,
        bool $matchModule
    ): ModuleFilterTransfer {
        $organizationTransfer = $this->createMock(OrganizationTransfer::class);
        $organizationTransfer->method('getNameOrFail')
            ->willReturn($matchOrganization ? static::CORRECT_NAME : static::WRONG_NAME);

        $applicationTransfer = $this->createMock(ApplicationTransfer::class);
        $applicationTransfer->method('getNameOrFail')
            ->willReturn($matchApplication ? static::CORRECT_NAME : static::WRONG_NAME);

        $moduleTransfer = $this->createMock(ModuleTransfer::class);
        $moduleTransfer->method('getNameOrFail')
            ->willReturn($matchModule ? static::CORRECT_NAME : static::WRONG_NAME);

        $filterTransfer = $this->createMock(ModuleFilterTransfer::class);
        $filterTransfer->method('getOrganization')->willReturn($organizationTransfer);
        $filterTransfer->method('getApplication')->willReturn($applicationTransfer);
        $filterTransfer->method('getModule')->willReturn($moduleTransfer);

        return $filterTransfer;
    }
}
