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
        $classModifier->setMethodReturnValue($classInformationTransfer, 'getScalarValue', $value, false, '');
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
            './tests/_tests_files/test_integrator_config_module_class_modifier.php',
        );
        $classModifier = $this->getFactory()->createCommonClassModifier();
        $finder = $this->getFactory()->createClassNodeFinder();
        $value = 'getenv(\'FOOBAR\')';

        //Act
        $classModifier->setMethodReturnValue($classInformationTransfer, 'getLiteralValue', $value, true, '');
        $stmts = $finder->findMethodNode($classInformationTransfer, 'getLiteralValue')->stmts;

        //Assert
        $this->assertTrue(isset($stmts[0]));
        $this->assertTrue(get_class($stmts[0]) === Return_::class);
        $this->assertTrue(get_class($stmts[0]->expr) === FuncCall::class);
    }
}
