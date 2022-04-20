<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Integrator\Builder\ClassModifier;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Return_;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassModifierTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testSetMethodReturnValueSetScalarValue(): void
    {
        //Arrange
        $classInformationTransfer = $this->createClassInformationTransfer(
            '\Pyz\Zed\TestIntegratorDefault\TestIntegratorDefaultConfig',
            './tests/_tests_files/test_integrator_config_module_class_modifier.php',
        );
        $classModifier = $this->getFactory()->createCommonClassModifier();
        $finder = $this->getFactory()->createClassNodeFinder();
        $value = 'value_that_we_are_looking';

        //Act
        $classModifier->setMethodReturnValue($classInformationTransfer, 'getScalarValue', $value);
        $stmts = $finder->findMethodNode($classInformationTransfer, 'getScalarValue')->stmts;

        //Assert
        $this->assertTrue(isset($stmts[0]));
        $this->assertTrue(get_class($stmts[0]) === Return_::class);
        $this->assertSame($stmts[0]->expr->value, $value);
    }

    /**
     * @return void
     */
    public function testSetMethodReturnValueSetLiteralValue(): void
    {
        //Arrange
        $classInformationTransfer = $this->createClassInformationTransfer(
            '\Pyz\Zed\TestIntegratorDefault\TestIntegratorDefaultConfig',
            './tests/_tests_files/test_integrator_config_module_class_modifier.php',
        );
        $classModifier = $this->getFactory()->createCommonClassModifier();
        $finder = $this->getFactory()->createClassNodeFinder();
        $value = [
            'value' => 'getenv(\'FOOBAR\')',
            'is_literal' => true,
        ];

        //Act
        $classModifier->setMethodReturnValue($classInformationTransfer, 'getLiteralValue', $value);
        $stmts = $finder->findMethodNode($classInformationTransfer, 'getLiteralValue')->stmts;

        //Assert
        $this->assertTrue(isset($stmts[0]));
        $this->assertTrue(get_class($stmts[0]) === Return_::class);
        $this->assertTrue(get_class($stmts[0]->expr) === FuncCall::class);
    }
}
