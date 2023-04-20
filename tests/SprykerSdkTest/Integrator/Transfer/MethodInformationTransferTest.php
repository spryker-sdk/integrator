<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Transfer;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;
use SprykerSdk\Integrator\Transfer\MethodInformationTransfer;
use SprykerSdk\Integrator\Transfer\ModuleTransfer;

class MethodInformationTransferTest extends TestCase
{
    /**
     * @return void
     */
    public function testFromArray(): void
    {
        $transfer = new MethodInformationTransfer();

        $transfer->fromArray(
            [
                'name' => 'test val 1',
                'Name' => 'test val 2',
                'ReturnType' => new ClassMetadataTransfer(),
                'invalidPropertyName' => '',
            ],
            true,
        );

        $this->assertEquals('test val 2', $transfer->getName());
        $this->assertEquals('test val 2', $transfer->getNameOrFail());

        $res = $transfer->getReturnType();
        $this->assertInstanceOf(ClassMetadataTransfer::class, $res);

        $res = $transfer->getReturnTypeOrFail();
        $this->assertInstanceOf(ClassMetadataTransfer::class, $res);

        $transfer->setName(null);
        $this->assertNull($transfer->getName());

        $this->expectException(InvalidArgumentException::class);
        $transfer->fromArray(
            [
                'invalidPropertyName' => '',
            ],
        );
    }

    /**
     * @return void
     */
    public function testModifiedToArray(): void
    {
        $transfer = new MethodInformationTransfer();

        $transfer->fromArray(
            [
                'name' => [
                    'name' => 'test val 1',
                ],
                'Name' => [
                    'name' => 'test val 2',
                ],
                'returnType' => new MethodInformationTransfer(),
                'ReturnType' => new ClassMetadataTransfer(),
            ],
        );

        $expRes1 = [
            'name' => [
                'name' => 'test val 2',
            ],
            'returnType' => [],
        ];
        $this->assertSame($expRes1, $transfer->modifiedToArray(true, true));

        $expRes2 = [
            'name' => [
                'name' => 'test val 2',
            ],
            'return_type' => [],
        ];
        $this->assertSame($expRes2, $transfer->modifiedToArray(true, false));

        $expResName3 = [
            'name' => 'test val 2',
        ];
        $res3 = $transfer->modifiedToArray(false, true);
        $this->assertSame($expResName3, $res3['name']);
        $this->assertInstanceOf(ClassMetadataTransfer::class, $res3['returnType']);

        $expResName4 = [
            'name' => 'test val 2',
        ];
        $res4 = $transfer->modifiedToArray(false, false);
        $this->assertSame($expResName4, $res4['name']);
        $this->assertInstanceOf(ClassMetadataTransfer::class, $res4['return_type']);
    }

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $transfer = new MethodInformationTransfer();

        $transfer->fromArray(
            [
                'name' => [
                    'name' => 'test val 1',
                ],
                'Name' => [
                    'name' => 'test val 2',
                ],
                'returnType' => new MethodInformationTransfer(),
                'ReturnType' => new ModuleTransfer(),
            ],
        );

        $expResName1 = [
            'name' => [
                'name' => 'test val 2',
            ],
        ];
        $res1 = $transfer->toArray(true, true);
        $this->assertSame($expResName1['name'], $res1['name']);
        $this->assertInstanceOf(ModuleTransfer::class, $res1['returnType']);

        $expResName2 = [
            'name' => [
                'name' => 'test val 2',
            ],
        ];
        $res2 = $transfer->toArray(true, false);
        $this->assertSame($expResName2['name'], $res2['name']);
        $this->assertInstanceOf(ModuleTransfer::class, $res2['return_type']);

        $expResName3 = [
            'name' => 'test val 2',
        ];
        $res3 = $transfer->toArray(false, true);
        $this->assertSame($expResName3, $res3['name']);
        $this->assertInstanceOf(ModuleTransfer::class, $res3['returnType']);

        $expResName4 = [
            'name' => 'test val 2',
        ];
        $res4 = $transfer->toArray(false, false);
        $this->assertSame($expResName4, $res4['name']);
        $this->assertInstanceOf(ModuleTransfer::class, $res4['return_type']);
    }
}
