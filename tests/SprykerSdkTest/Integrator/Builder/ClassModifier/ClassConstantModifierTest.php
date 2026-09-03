<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\ClassModifier;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use SprykerSdkTest\Integrator\BaseTestCase;

class ClassConstantModifierTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected const TEST_INTEGRATOR_DEFAULT_CONFIG_PATH = '/src/Pyz/Zed/TestIntegratorDefault/TestIntegratorDefaultConfig.php';

    /**
     * @return void
     */
    public function testSetConstantWithClassConstantReferenceValueIsStoredAsExpressionNotString(): void
    {
        //Arrange
        $classInformationTransfer = $this->createClassInformationTransfer(
            '\Pyz\Zed\TestIntegratorDefault\TestIntegratorDefaultConfig',
            $this->getProjectMockCurrentPath() . static::TEST_INTEGRATOR_DEFAULT_CONFIG_PATH,
        );
        $classConstantModifier = $this->getFactory()->createClassConstantModifier();
        $finder = $this->getFactory()->createClassNodeFinder();
        $value = '\Monolog\Logger::WARNING';

        //Act
        $classConstantModifier->setConstant($classInformationTransfer, 'DEFAULT_EVENT_LOGGER_MIN_LEVEL', $value, false);
        $constantNode = $finder->findConstantNode($classInformationTransfer, 'DEFAULT_EVENT_LOGGER_MIN_LEVEL');

        //Assert
        $this->assertInstanceOf(ClassConst::class, $constantNode);
        $constantValueExpr = $constantNode->consts[0]->value;
        $this->assertNotInstanceOf(String_::class, $constantValueExpr);
        $this->assertInstanceOf(ClassConstFetch::class, $constantValueExpr);
        $this->assertSame('Monolog\Logger', $constantValueExpr->class->toString());
        $this->assertSame('WARNING', $constantValueExpr->name->toString());
    }
}
