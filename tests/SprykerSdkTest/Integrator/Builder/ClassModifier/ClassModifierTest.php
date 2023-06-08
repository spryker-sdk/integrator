<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\ClassModifier;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Return_;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassModifierTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const TEST_INTEGRATOR_DEFAULT_CONFIG_PATH = '/src/Pyz/Zed/TestIntegratorDefault/TestIntegratorDefaultConfig.php';

    /**
     * @return void
     */
    public function testSetMethodReturnValueSetScalarValue(): void
    {
        //Arrange
        $classInformationTransfer = $this->createClassInformationTransfer(
            '\Pyz\Zed\TestIntegratorDefault\TestIntegratorDefaultConfig',
            $this->getProjectMockCurrentPath() . static::TEST_INTEGRATOR_DEFAULT_CONFIG_PATH,
        );
        $classModifier = $this->getFactory()->createCommonClassModifier();
        $finder = $this->getFactory()->createClassNodeFinder();
        $value = 'value_that_we_are_looking';

        //Act
        $classModifier->createClassMethod($classInformationTransfer, 'getScalarValue', $value, false, '');
        $stmts = $finder->findMethodNode($classInformationTransfer, 'getScalarValue')->stmts;

        //Assert
        $this->assertTrue(isset($stmts[0]));
        $this->assertTrue(get_class($stmts[0]) === Return_::class);
        $this->assertSame($stmts[0]->expr->name->parts[0], $value);
    }

    /**
     * @return void
     */
    public function testSetMethodReturnValueSetLiteralValue(): void
    {
        //Arrange
        $classInformationTransfer = $this->createClassInformationTransfer(
            '\Pyz\Zed\TestIntegratorDefault\TestIntegratorDefaultConfig',
            $this->getProjectMockCurrentPath() . static::TEST_INTEGRATOR_DEFAULT_CONFIG_PATH,
        );
        $classModifier = $this->getFactory()->createCommonClassModifier();
        $finder = $this->getFactory()->createClassNodeFinder();
        $value = 'getenv(\'FOOBAR\')';

        //Act
        $classModifier->createClassMethod($classInformationTransfer, 'getLiteralValue', $value, true, '');
        $stmts = $finder->findMethodNode($classInformationTransfer, 'getLiteralValue')->stmts;

        //Assert
        $this->assertTrue(isset($stmts[0]));
        $this->assertTrue(get_class($stmts[0]) === Return_::class);
        $this->assertTrue(get_class($stmts[0]->expr) === FuncCall::class);
    }
}
